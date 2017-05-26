<?php
namespace SeanKndy\SMVC\Session;

/*
 * Store sessions within `sessions` table
 *
 */

class DatabaseHandler implements \SessionHandlerInterface
{
    private $db;

    public function __construct($db) {
        $this->setDb($db);
    }

    public function setDb($db) {
        $this->db = $db;
    }

    // implementing interface methods
    public function open($savePath, $sessionName) {
        return true;
    }

    public function close() {
        return true;
    }

    public function read($id) {
        try {
            $sth = $this->db->prepare("select sess_data from sessions where sess_id = ?");
            $sth->execute(array($id));
            if ($row = $sth->fetch(PDO::FETCH_OBJ)) {
                return $row->sess_data;
            }
        } catch (PDOException $e) {
            return '';
        }
    }

    public function write($id, $data) {
        try {
            $sth = $this->db->prepare("select sess_id from sessions where sess_id = ?");
            $sth->execute(array($id));
            if ($sth->fetch()) {
                $sql = "update sessions set sess_data = ?, last_updated = now() where sess_id = ?";
            } else {
                $sql = "insert into sessions (sess_data, sess_id, last_updated) values(?, ?, now())";
            }
            $sth = $this->db->prepare($sql);
            $sth->execute(array($data, $id));
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function destroy($id) {
        try {
            $sth = $this->db->prepare("delete from sessions where sess_id = ?");
            $sth->execute(array($id));
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function gc($maxlifetime) {
        try {
            $sth = $this->db->prepare("delete from sessions where now()-last_updated >= ?");
            $sth->execute(array($maxlifetime));
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function __set($var, $val) {
        return $this->set($var, $val);
    }
    
    public function set($var, $val) {
        return ($_SESSION[$var] = $val);
    }
    
    public function clear() {
        $_SESSION = [];
    }
    
    public function __unset($var) {
        if (isset($_SESSION[$var])) {
            unset($_SESSION[$var]);
        }
    }
    
    public function get($var) {
        return isset($_SESSION[$var]) ? $_SESSION[$var] : '';
    }
    
    public function __get($var) {
        return $this->get($var);
    }
}
