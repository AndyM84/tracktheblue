DROP DATABASE IF EXISTS ttb;

CREATE DATABASE ttb;

USE ttb;


CREATE TABLE civilian (
	civilian_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    age INT NOT NULL,
    ethnicity VARCHAR(20) NOT NULL, -- maybe some consistency issue here?
    gender ENUM('male', 'female', 'other', 'prefer not to say') NOT NULL
	
);

CREATE TABLE citation (
	-- if citation did not occur, no data inserted?
	citation_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    citation_classification ENUM('felony', 'misdemeanou', 'infraction'),
    mandotory_court BOOLEAN NOT NULL,
	citation_date DATE NOT NULL,
    citation_time TIME NOT NULL,
    fine_ammount INT NOT NULL
    -- video, how to? text to link?
	
   
   );
   
CREATE TABLE eventcontext (
	event_id INT AUTO_INCREMENT PRIMARY KEY,
	search_conducted BOOLEAN NOT NULL,
    probable_cause TEXT,
    destination TEXT,
    civilian_id INT,
    citation_id INT,
    FOREIGN KEY (civilian_id) REFERENCES civilian (civilian_id),
	FOREIGN KEY (citation_id) REFERENCES citation (citation_id)
    
   );
   
   