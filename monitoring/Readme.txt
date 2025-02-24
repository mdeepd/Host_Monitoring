Welcome to the Host_Monitoring wiki!

How to install Host_Monitoring?

Copy all the files to your host directory.

1. Create the Database and Tables (I am using Mariadb)

Creates a database named “monitoring” and two tables: one for users and one for monitored items.
Login to Mariadb:
Copy and paste these db commands to create database and users:

CREATE DATABASE IF NOT EXISTS monitoring;
USE monitoring;

-- Users table (for login)

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

-- Insert a default user (username: admin, password: admin123)
-- (Note: For production, use PHP’s password_hash instead of MD5)

INSERT INTO users (username, password) VALUES ('admin', MD5('admin123'));

-- Monitored items table

CREATE TABLE IF NOT EXISTS monitored_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('IP','Domain') NOT NULL,
  address VARCHAR(255) NOT NULL,`<br>
  timer INT NOT NULL,  -- timer in seconds
  last_checked DATETIME DEFAULT NULL,
  status ENUM('up','down') DEFAULT 'down',
  latency FLOAT DEFAULT NULL
);

CREATE USER 'your_db_user'@'localhost' IDENTIFIED BY 'your_db_password';
GRANT ALL PRIVILEGES ON monitoring.* TO 'your_db_user'@'localhost';
FLUSH PRIVILEGES;
your_db_user = change with any user name you want.
your_db_password = change with any password you want. Use strong password.
----------------------------------

2. Add db connection in the Configuration File.
Edit the config.php to connect to the database. (Replace the placeholders with your actual database username and password.)
3. How It Works:
**Login:**
Users visit login.php. Once authenticated, they are redirected to dashboard.php.

**Dashboard:**
The dashboard lists all monitored items. For each item, JavaScript sets a timer (based on the “timer” field) that calls check_status.php via AJAX. That script pings the specified IP or domain and returns its status and latency. The dashboard then updates the table accordingly.

**CRUD Operations:**
You can add new monitors using add_item.php, update them with edit_item.php, or remove them using delete_item.php.

**Security:**
The dashboard and monitoring endpoints check for a valid session. (For production, remember to implement additional security measures.)

This should give you a complete starting point for a monitoring webpage that checks reachability via ICMP and displays latency. You can expand and refine the design, add error handling, and improve security as needed.