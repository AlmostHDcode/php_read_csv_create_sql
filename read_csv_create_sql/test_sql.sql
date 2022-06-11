CREATE TABLE test_csv_customers (
    cust_id INT NOT NULL AUTO_INCREMENT,
    cust_username VARCHAR(25) NOT NULL,
    cust_pass VARCHAR(128) NOT NULL,
    cust_f_name VARCHAR(255) NOT NULL,
    cust_m_initial CHAR(1) NULL,
    cust_l_name VARCHAR(255) NOT NULL,
    date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cust_status ENUM("active", "inactive") NOT NULL DEFAULT("active"),
    PRIMARY KEY(cust_id)
);