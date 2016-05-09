<?php

/**
 * Class ModelPaymentMTPayment
 */
class ModelPaymentMTPayment extends Model
{

    /**
     *
     */
    public function install()
    {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mttransactions` (
              `transaction` varchar(255) NOT NULL,
              `amount` DECIMAL(10,2) NOT NULL,
              `order` int(10) NOT NULL,
              `websocket` varchar(255) NULL,
              `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`transaction`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->db->query(
            "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mtcallbacks` (
              `callback` VARCHAR(255) NOT NULL,
              `transaction` VARCHAR(255) NOT NULL,
              `amount` DECIMAL(10,2) NOT NULL,
              `callback_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`callback`));");
    }

    /**
     *
     */
    public function uninstall()
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "mttransactions`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "mtcallbacks`;");
    }
}
