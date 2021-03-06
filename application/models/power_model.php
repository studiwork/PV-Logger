<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Power_model extends CI_Model 
{
    
    function getCurrentP($inv = NULL)
    {
        $dailydate = date('Y-m-d');
        //Nur bestimmte Wechselrichterdaten abrufen, sonst alle drei
        if($inv != NULL)
        {
            $this->db->select_max('time');
            $query = $this->db->get('pv_data');
            $row = $query->row();
            $latest = $row->time;

            $this->db->select('power')->where('inverter_id', $inv)->where('date', $dailydate)->where('time', $latest);
            $query = $this->db->get('pv_data');
            $row = $query->row();
            if((time() - strtotime( date('Y-m-d').$latest) < 60))
            {
                $result = $row->power;
            }
            else 
            {
                $result = 0;
            }

            $cols = array(array("label" => "Watt","type" => "number"));
            $rows = array(array('c' => array( array( 'v' => $result))));
            echo '{ "cols": '.json_encode($cols).', "rows":'.json_encode($rows).'}';
        }
        else
        {
            $this->db->select_max('time');
            $query = $this->db->get('pv_data');
            $row = $query->row();
            $latest = $row->time;

            $this->db->select_sum('power')->where('date', $dailydate)->where('time', $latest);
            $query = $this->db->get('pv_data');
            $row = $query->row();
            if((time() - strtotime( date('Y-m-d').$latest) < 60))
            {
                $result = $row->power;
            }
            else 
            {
                $result = 0;
            }
            
            $cols = array(array("label" => "Watt","type" => "number"));
            $rows = array(array('c' => array( array( 'v' => $result))));
            echo '{ "cols": '.json_encode($cols).', "rows":'.json_encode($rows).'}';
            
        }
    }
    
    function getDailyPower()
    {
        //aktuelles Datum im richtigen Format speichern
        $dailydate = date('Y-m-d');
        
        //alle heutigen Daten auslesen und die ergebnisse der einzelnen wechselrichter summieren
        $this->db->select('time')->select_sum('power')->where('date', $dailydate)->group_by('time');
        $query = $this->db->get('pv_data');

        //Rows Array mit daten füllen
        $rows = array();
        foreach ($query->result() as $row)
        {
            $res = array('c' => array( array( 'v' => $row->time),array( 'v' => $row->power)));
            array_push($rows, $res);
        }

        $cols = array(array("label" => "Stunde","type" => "string"),array("label" => "Watt","type" => "number"));        
        echo '{ "cols": '.json_encode($cols).', "rows":'.json_encode($rows).'}';
    }
    
    function getMonthPower()
    {
        $actualMonth = date('Y-m');
        $firstMonthDay = $actualMonth."-01";
        
        $this->db->where('date >=', $firstMonthDay)->order_by('time')->order_by('date')->group_by('date');
        $query = $this->db->get('pv_data');
        
        //Rows Array mit daten füllen
        $rows = array();
        foreach ($query->result() as $row)
        {
            $res = array('c' => array( array( 'v' => $row->date),array( 'v' => $row->daily_energy)));
            array_push($rows, $res);
        }

        $cols = array(array("label" => "Stunde","type" => "string"),array("label" => "Wh","type" => "number"));        
        echo '{ "cols": '.json_encode($cols).', "rows":'.json_encode($rows).'}';
        
    }
    
    
}
