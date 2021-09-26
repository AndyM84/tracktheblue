DROP DATABASE IF EXISTS `ttb`;

CREATE DATABASE `ttb`;

USE `ttb`;


CREATE TABLE `civilian` (
	`civilian_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `age` INT NOT NULL,
    `ethnicity` VARCHAR(20) NOT NULL, -- maybe some consistency issue here?
    `gender` INT NOT NULL
	
);

CREATE TABLE `citation` (
	-- if citation did not occur, no data inserted?
	`citation_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `citation_classification` INT NOT NULL,
    `mandatory_court` BOOLEAN NOT NULL,
	`citation_date` DATETIME NOT NULL,
    `fine_ammount` INT NOT NULL,
    `video` TEXT
	
   );
   
CREATE TABLE eventcontext (
	`event_id` INT AUTO_INCREMENT PRIMARY KEY,
	`search_conducted` BOOLEAN NOT NULL,
    `stated_probable_cause` TEXT,
    `destination` TEXT,
    `civilian_id` INT,
    `citation_id` INT,
    FOREIGN KEY (civilian_id) REFERENCES civilian (civilian_id),
	FOREIGN KEY (citation_id) REFERENCES citation (citation_id)
    
   );
   
   