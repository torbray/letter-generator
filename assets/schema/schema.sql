SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS employee;
DROP TABLE IF EXISTS job_title;

DROP TABLE IF EXISTS relationship;
DROP TABLE IF EXISTS account;
DROP TABLE IF EXISTS account_type;
DROP TABLE IF EXISTS relationship_role;
DROP TABLE IF EXISTS customer;
DROP TABLE IF EXISTS title;

DROP TABLE IF EXISTS audit_log;

DROP TABLE IF EXISTS letter_variable;

SET FOREIGN_KEY_CHECKS = 1;


CREATE TABLE job_title (
	job_id			int NOT NULL AUTO_INCREMENT,
	job_title		varchar(255) NOT NULL,
	access_level	int NOT NULL,
    PRIMARY KEY (job_id)
	);

CREATE TABLE employee (
	employee_id	int NOT NULL AUTO_INCREMENT,
	first_name	varchar(255) NOT NULL,
	last_name	varchar(255) NOT NULL,
	username	varchar(255) NOT NULL,
	password 	varchar(255) NOT NULL,
	job_id		int,
	change_pwd	ENUM('Y', 'N') DEFAULT 'N',
	CONSTRAINT fk_job_id FOREIGN KEY (job_id) REFERENCES job_title (job_id),
    PRIMARY KEY (employee_id)
	);

CREATE TABLE title (
	title_id		int NOT NULL AUTO_INCREMENT,
    title_desc		char(40) NOT NULL,
    PRIMARY KEY (title_id)
	);

CREATE TABLE customer (
	customer_id	int NOT NULL AUTO_INCREMENT,
    first_name	varchar(255) NOT NULL,
    last_name	varchar(255) NOT NULL,
	address		text,
    title_id	int,
	dob			date NOT NULL,
    PRIMARY KEY (customer_id),
    CONSTRAINT fk_title_id FOREIGN KEY (title_id) REFERENCES title (title_id)
	);

CREATE TABLE relationship_role (
	role_id			int NOT NULL AUTO_INCREMENT,
    role_desc		char(50) NOT NULL,
    role_desc_abbrv	char(40) NOT NULL,
    PRIMARY KEY (role_id)
	);
	
CREATE TABLE account_type (
	type_id		int NOT NULL AUTO_INCREMENT,
    type_name	char(50) NOT NULL,
    PRIMARY KEY (type_id)
	);
	
CREATE TABLE account (
	account_id		int NOT NULL AUTO_INCREMENT,
	account_name	varchar(255),
    type_id			int NOT NULL,
	balance			decimal NOT NULL,
	open_date		date NOT NULL,
	close_date		date,
    PRIMARY KEY (account_id),
    CONSTRAINT fk_type_id FOREIGN KEY (type_id) REFERENCES account_type (type_id)
	);

CREATE TABLE relationship (
	relationship_id	int NOT NULL AUTO_INCREMENT,
    customer_id		int NOT NULL,
    account_id		int NOT NULL,
    role_id			int NOT NULL,
    PRIMARY KEY (relationship_id),
    CONSTRAINT relationship_customer_id FOREIGN KEY (customer_id) REFERENCES customer (customer_id),
	CONSTRAINT relationship_account_id FOREIGN KEY (account_id) REFERENCES account (account_id),
	CONSTRAINT fk_role_id FOREIGN KEY (role_id) REFERENCES relationship_role (role_id)
	);

CREATE TABLE audit_log (
    log_id 			int NOT NULL AUTO_INCREMENT,
    log_date 		date NOT NULL,
    log_time 		time NOT NULL,
    letter_type 	varchar(255) NOT NULL,
	employee_id		int NOT NULL,
    customer_id		int NOT NULL,
    account_id 		int NOT NULL,
	PRIMARY KEY (log_id),
	CONSTRAINT audit_log_employee_id FOREIGN KEY (employee_id) REFERENCES employee (employee_id),
    CONSTRAINT audit_log_customer_id FOREIGN KEY (customer_id) REFERENCES customer (customer_id),
    CONSTRAINT audit_log_account_id FOREIGN KEY (account_id) REFERENCES account (account_id)
);
	
CREATE TABLE letter_variable (
	variable_id	int NOT NULL AUTO_INCREMENT,
	category	varchar(255) NOT NULL,
	letter_key	varchar(255) NOT NULL,
	method		varchar(255) NOT NULL,
	box			ENUM('short', 'long') DEFAULT 'short',
    PRIMARY KEY (variable_id)
	);

INSERT INTO title (title_id, title_desc)
VALUES 
	(1, "Mr"),
	(2, "Mrs"),
	(3, "Master"),
	(4, "Miss"),
	(5, "Ms"),
	(6, "Mx"),
	(7, "Dr");
	
INSERT INTO `customer` (`first_name`,`last_name`,`address`,`title_id`,`dob`)
VALUES
  ("Leo","Dotson","Ap #519-2541 Nam Av.",2,"1991-03-04"),
  ("Carson","Saunders","P.O. Box 886, 7448 Proin Ave",2,"2003-04-26"),
  ("Marsden","Haney","9287 Pede Avenue",6,"1981-08-05"),
  ("Vivien","Kramer","P.O. Box 552, 1063 Nec Street",6,"1978-07-06"),
  ("Yeo","Langley","872-6954 Nunc Road",7,"2005-02-23"),
  ("Katell","Solomon","712-6082 Orci. Ave",6,"2022-09-17"),
  ("Mariko","Burnett","885-3205 Est, Av.",5,"1982-09-03"),
  ("Darrel","Holden","P.O. Box 328, 8267 Ut, Rd.",3,"1974-02-23"),
  ("Kylynn","Mckee","Ap #892-748 Dapibus Road",3,"1973-11-11"),
  ("Sasha","Dawson","277-5937 Fusce St.",7,"2009-11-18");
INSERT INTO `customer` (`first_name`,`last_name`,`address`,`title_id`,`dob`)
VALUES
  ("Gay","Bird","Ap #469-9974 Lacus. Street",1,"1975-11-05"),
  ("Marvin","Page","Ap #752-6188 Erat Road",2,"1988-07-16"),
  ("Samuel","Rios","Ap #430-3933 Tellus Avenue",2,"1970-06-07"),
  ("Jayme","Castillo","144-7432 Primis Avenue",5,"1953-06-15"),
  ("Leah","Hernandez","Ap #906-9465 Nulla Street",5,"2013-06-29"),
  ("Channing","Moses","157-234 Adipiscing St.",4,"1982-03-24"),
  ("Nita","Wilkerson","393-186 Tellus Ave",5,"1985-08-12"),
  ("Abraham","Conley","Ap #977-7492 Mauris. Ave",3,"1977-02-01"),
  ("Deanna","Solomon","4964 Tempor Road",2,"1977-03-02"),
  ("Erasmus","Osborne","957-405 Id, Rd.",2,"1985-07-13");
INSERT INTO `customer` (`first_name`,`last_name`,`address`,`title_id`,`dob`)
VALUES
  ("Ulla","Holloway","Ap #400-4642 Et Rd.",2,"2004-02-09"),
  ("Robin","Potts","8924 Fringilla Avenue",3,"2016-03-24"),
  ("Ahmed","Hogan","860-3090 Egestas Ave",5,"1997-03-21"),
  ("Britanney","Hudson","P.O. Box 765, 7881 Nullam Av.",5,"1984-04-06"),
  ("Damian","Horton","444-587 Posuere, Street",2,"1998-11-11"),
  ("Ishmael","Malone","P.O. Box 401, 5364 Quis, Av.",4,"1980-06-29"),
  ("Brenden","Beasley","P.O. Box 228, 6366 Tortor, St.",3,"1966-01-04"),
  ("Garrison","Hood","209-9841 Egestas Av.",7,"1997-03-05"),
  ("Irma","Ortega","175-153 Turpis St.",7,"2012-09-03"),
  ("Risa","Harrington","940-5134 Magnis Road",6,"2017-10-03");
INSERT INTO `customer` (`first_name`,`last_name`,`address`,`title_id`,`dob`)
VALUES
  ("Kim","Hill","328-3057 Rutrum Road",2,"1957-12-04"),
  ("Violet","Hebert","Ap #694-2990 Mollis St.",6,"1997-04-22"),
  ("Murphy","Martinez","P.O. Box 295, 8009 Risus. Avenue",5,"1994-06-25"),
  ("Ryan","Jensen","Ap #312-2192 A, Street",1,"2015-06-04"),
  ("Erasmus","Case","P.O. Box 468, 651 Nunc Avenue",6,"2012-08-05"),
  ("Kaseem","Hanson","Ap #157-273 Odio, Av.",5,"2016-05-11"),
  ("Brendan","Leblanc","338-1309 Luctus St.",5,"2012-11-14"),
  ("Emmanuel","Hobbs","Ap #606-4610 Cras Street",6,"1981-01-17"),
  ("Avram","Anderson","6855 Ante Street",4,"2004-01-14"),
  ("Quintessa","Schmidt","P.O. Box 153, 9314 Eget, Street",7,"2015-12-11");
INSERT INTO `customer` (`first_name`,`last_name`,`address`,`title_id`,`dob`)
VALUES
  ("Tasha","Workman","P.O. Box 360, 8297 Id Ave",7,"1952-01-22"),
  ("Kennedy","Cross","722-8796 Vel Road",7,"2008-09-23"),
  ("Blossom","Lynch","6116 Enim, Road",2,"1983-02-14"),
  ("Jada","Mack","Ap #738-5293 Ornare. Ave",5,"2011-03-04"),
  ("MacKenzie","Holden","P.O. Box 590, 5786 Sed Rd.",2,"1966-03-17"),
  ("Dorothy","Dalton","P.O. Box 247, 722 Consequat Rd.",3,"1957-10-24"),
  ("Linus","Stout","6904 Mauris Avenue",7,"1991-06-11"),
  ("Echo","Holland","935-8063 Urna. St.",4,"1953-10-28"),
  ("Renee","Davenport","838-3564 Enim. Av.",5,"1966-12-10"),
  ("Teegan","Fischer","4352 Nascetur Road",3,"1989-07-15");
INSERT INTO `customer` (`first_name`,`last_name`,`address`,`title_id`,`dob`)
VALUES
  ("Nolan","Villarreal","3025 A, Road",5,"2006-05-06"),
  ("Cathleen","Montgomery","P.O. Box 147, 4088 Fames Rd.",2,"1999-07-25"),
  ("Ignatius","Avery","P.O. Box 834, 4969 Phasellus Street",6,"1975-11-10"),
  ("Priscilla","Hogan","747-8532 Fringilla Ave",3,"1982-03-28"),
  ("Tyrone","Evans","Ap #437-4461 Elit, St.",6,"1952-04-16"),
  ("Zephania","Schultz","P.O. Box 434, 9884 Tempor St.",3,"1990-01-25"),
  ("Nasim","Stafford","628-4394 Enim St.",7,"1955-01-18"),
  ("Troy","Woods","5377 Est Avenue",5,"1964-02-22"),
  ("Bianca","Kerr","Ap #914-7658 Lacus, Rd.",1,"1961-05-17"),
  ("Quon","Valdez","Ap #932-4409 Vestibulum St.",7,"1967-02-02");
INSERT INTO `customer` (`first_name`,`last_name`,`address`,`title_id`,`dob`)
VALUES
  ("Stuart","Erickson","Ap #309-7862 Hendrerit Road",6,"1957-10-03"),
  ("Maxine","Pickett","706-897 Aliquet Road",4,"1996-12-04"),
  ("Bert","Randolph","P.O. Box 459, 4268 Eros. St.",4,"1955-10-16"),
  ("Nevada","Heath","Ap #826-1265 Nisi. Avenue",7,"1994-06-21"),
  ("Bradley","Cochran","3641 Facilisi. Rd.",7,"1986-10-12"),
  ("Noble","Hess","579-8441 Commodo St.",2,"1989-12-20"),
  ("Talon","Padilla","P.O. Box 117, 3336 Risus Street",7,"1987-10-13"),
  ("Noelani","Paul","Ap #991-182 Sed Street",4,"1954-10-02"),
  ("Charity","Hamilton","Ap #240-1649 In Av.",3,"1975-04-26"),
  ("Hayes","Weiss","7106 In St.",3,"1982-11-22");
INSERT INTO `customer` (`first_name`,`last_name`,`address`,`title_id`,`dob`)
VALUES
  ("Katelyn","Casey","129-2033 Erat Road",2,"1966-04-01"),
  ("Aquila","Franks","Ap #280-3149 Ac Av.",5,"1964-09-06"),
  ("Kylee","Drake","516-7751 Curabitur Road",4,"2009-10-05"),
  ("Kirestin","Mckay","5268 Phasellus Rd.",4,"1992-07-09"),
  ("Kalia","Berg","Ap #612-6152 Tempor St.",4,"2020-08-15"),
  ("Kasper","Sutton","Ap #682-6400 Vitae Rd.",5,"1989-07-28"),
  ("Wilma","Kelly","1080 Quis, Av.",3,"1955-09-01"),
  ("Joseph","Eaton","611-4426 Rhoncus. St.",4,"1972-06-14"),
  ("Uma","Contreras","P.O. Box 498, 6550 Libero St.",6,"1996-06-19"),
  ("Brennan","Powell","Ap #483-1266 Dolor Av.",1,"1985-07-06");
INSERT INTO `customer` (`first_name`,`last_name`,`address`,`title_id`,`dob`)
VALUES
  ("Harlan","Guerra","P.O. Box 608, 6847 Diam Road",5,"2011-12-10"),
  ("Iona","Wright","428-5410 Lectus St.",4,"1979-12-27"),
  ("Holly","Galloway","748-9539 Lectus. St.",4,"1975-06-06"),
  ("Hannah","Conner","Ap #794-1791 Ante St.",6,"2021-08-17"),
  ("Philip","Fleming","529-8170 Tincidunt, Ave",4,"1985-11-06"),
  ("Mason","Lindsey","675-2471 Donec Rd.",5,"2012-10-22"),
  ("Chaney","Oneal","8812 Dapibus St.",5,"1966-03-19"),
  ("Davis","Hahn","P.O. Box 780, 9434 Non, Av.",5,"1965-03-19"),
  ("Dalton","Steele","Ap #856-6609 Tempor Road",1,"2016-09-09"),
  ("Chancellor","Leonard","P.O. Box 695, 6336 Malesuada Avenue",4,"1978-02-23");
INSERT INTO `customer` (`first_name`,`last_name`,`address`,`title_id`,`dob`)
VALUES
  ("Kylee","Houston","977-6131 Iaculis, St.",6,"1957-05-30"),
  ("Kuame","Blake","659-8531 Vivamus Rd.",7,"1976-02-04"),
  ("Noah","Villarreal","Ap #699-8426 Egestas. St.",6,"1962-03-30"),
  ("Stewart","Freeman","Ap #239-5269 Metus Street",3,"1984-04-11"),
  ("Cameron","Mejia","Ap #467-5580 Viverra. Street",7,"1952-02-17"),
  ("Zeus","Wilcox","Ap #523-9673 Magna Avenue",7,"2001-12-05"),
  ("Richard","Calhoun","2390 Vitae Rd.",3,"2009-07-31"),
  ("Hilary","Burks","670-5902 Auctor Road",3,"1983-01-04"),
  ("Brynn","Calhoun","717-5437 Sem Avenue",7,"2008-06-17"),
  ("Vanna","Hopper","7776 Velit Rd.",3,"1963-09-17");

 
INSERT INTO `job_title` (`job_title`,`access_level`)
VALUES
	("Undetermined", 0),
	("Consultant", 1),
	("Admin", 2);
	
INSERT INTO `letter_variable` (`category`,`letter_key`, `method`, `box`)
VALUES
	("customer", "name-full", "getFullName", "short"),
	("customer", "name-formal", "getFormalName", "short"),
	("customer", "address", "getAddress", "long"),
	("account", "name", "generateSampleAccountName", "long"),
	("account", "type", "getType", "short"),
	("account", "number", "getNumber", "short"),
	("account", "balance", "getBalance", "short"),
	("consultant", "name", "getName", "short"),
	("consultant", "title", "getTitle", "short");
	
INSERT INTO `account_type` (`type_name`)
VALUES
	("ANZ Go Account"),
	("ANZ Jumpstart Account"),
	("ANZ Freedom Account"),
	("ANZ Serious Saver Account"),
	("ANZ Online Account");

INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (4,"81.46","1973-08-28"),
  (4,"1902.08","1977-05-29"),
  (1,"8098.94","1957-02-23"),
  (4,"6513.82","2016-08-29"),
  (3,"6526.61","2014-04-26"),
  (4,"6461.55","1957-12-01"),
  (4,"3134.95","1978-04-28"),
  (5,"2214.41","1989-12-09"),
  (1,"2693.62","2001-03-22"),
  (3,"7288.58","1986-10-02");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (1,"7308.48","2012-05-12"),
  (3,"3505.76","1952-05-18"),
  (4,"6012.12","1949-05-24"),
  (1,"9463.13","1986-12-08"),
  (3,"528.89","1970-05-01"),
  (3,"6521.98","2006-02-20"),
  (5,"89.14","1984-05-31"),
  (4,"4882.04","1988-12-04"),
  (2,"4756.83","1996-11-14"),
  (1,"7618.05","1969-11-30");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (3,"7097.81","2020-01-06"),
  (2,"8722.73","2000-05-19"),
  (3,"1757.42","2014-01-17"),
  (2,"1201.24","1989-07-22"),
  (5,"4885.19","1995-09-24"),
  (3,"3221.47","1977-03-18"),
  (4,"559.09","1962-07-04"),
  (3,"1954.27","1956-06-23"),
  (4,"2278.29","2003-04-21"),
  (5,"4461.16","1963-10-05");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (4,"7232.35","1983-05-22"),
  (2,"195.73","2013-07-01"),
  (3,"8213.71","1988-08-18"),
  (4,"387.05","2014-03-10"),
  (5,"8187.38","1965-04-21"),
  (3,"6530.11","1951-09-03"),
  (2,"4982.72","2003-04-02"),
  (2,"755.18","1969-12-09"),
  (5,"4371.54","1977-05-12"),
  (1,"6965.21","2008-04-08");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (5,"8954.72","1975-01-26"),
  (3,"9426.40","2011-12-30"),
  (2,"8240.24","1977-06-24"),
  (4,"146.05","1957-11-08"),
  (2,"3332.27","1977-08-23"),
  (3,"8089.24","2009-11-15"),
  (2,"2306.19","1990-02-03"),
  (3,"837.89","1961-10-30"),
  (2,"607.82","1967-01-27"),
  (3,"6074.54","2002-08-20");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (5,"952.07","1958-07-12"),
  (1,"1912.96","2008-06-23"),
  (4,"2187.70","1968-01-12"),
  (5,"150.58","2012-04-25"),
  (1,"1265.15","2014-05-07"),
  (1,"6259.51","1978-04-12"),
  (3,"9949.09","2019-03-28"),
  (3,"6278.35","2021-06-10"),
  (5,"2733.64","1999-10-16"),
  (5,"3672.30","1972-12-01");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (2,"9378.95","1945-04-13"),
  (4,"4982.45","1988-02-12"),
  (3,"5836.43","1974-07-11"),
  (4,"7292.38","1964-06-09"),
  (3,"8117.92","1985-03-06"),
  (5,"7684.03","2020-07-10"),
  (3,"7017.02","1983-09-19"),
  (4,"7143.55","1966-06-20"),
  (3,"3167.22","2006-10-28"),
  (3,"7447.89","1982-02-04");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (2,"1958.91","1961-01-24"),
  (2,"2150.78","1988-04-27"),
  (2,"7496.28","2010-11-19"),
  (2,"7417.18","1981-03-21"),
  (1,"6278.51","1968-09-30"),
  (2,"336.55","1994-08-26"),
  (3,"2699.05","1950-07-05"),
  (1,"9758.66","1943-06-30"),
  (4,"7300.25","2019-01-24"),
  (4,"2405.36","2018-07-20");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (3,"3129.82","2013-11-06"),
  (4,"6501.72","1957-10-13"),
  (3,"4405.76","1983-09-09"),
  (3,"36.20","1975-11-08"),
  (1,"586.15","1959-06-20"),
  (1,"6846.32","1997-08-03"),
  (4,"5626.25","1961-05-27"),
  (4,"2958.58","1985-09-16"),
  (2,"4740.04","1986-12-29"),
  (4,"7376.10","2018-03-30");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (3,"3256.80","1949-02-10"),
  (4,"813.91","1966-07-13"),
  (2,"2895.31","2018-03-26"),
  (2,"2009.83","1988-08-11"),
  (1,"1571.94","1960-02-27"),
  (5,"438.52","1943-11-07"),
  (1,"5189.36","1963-06-29"),
  (5,"6142.28","1949-06-25"),
  (2,"3403.71","2000-10-02"),
  (1,"7490.55","2016-04-10");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (2,"4383.39","1960-02-16"),
  (2,"4949.81","1965-01-31"),
  (2,"487.65","2021-02-23"),
  (3,"3491.39","2004-06-02"),
  (4,"2899.42","1988-01-07"),
  (3,"1890.43","1968-12-02"),
  (4,"3104.24","1963-01-09"),
  (3,"7628.38","1955-11-07"),
  (2,"4526.09","2014-08-12"),
  (1,"2626.25","1992-04-04");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (3,"9636.91","1981-11-29"),
  (4,"6055.38","2000-05-09"),
  (3,"8754.04","1999-07-01"),
  (3,"392.23","1972-05-30"),
  (2,"9753.01","1988-12-28"),
  (4,"3578.85","1973-10-11"),
  (1,"7105.32","2005-06-13"),
  (1,"6753.96","1968-12-30"),
  (1,"6562.31","1966-11-20"),
  (4,"3513.01","2010-09-05");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (3,"1181.37","1969-09-24"),
  (3,"9166.62","2012-03-05"),
  (4,"1182.50","1954-11-27"),
  (2,"1308.58","1956-03-23"),
  (3,"2315.00","1983-06-06"),
  (3,"6182.03","2006-02-21"),
  (4,"9226.66","1954-08-22"),
  (5,"801.79","1987-08-09"),
  (2,"4069.41","2016-02-08"),
  (1,"1115.89","1945-02-24");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (2,"2269.32","1946-04-25"),
  (3,"1537.75","1960-09-09"),
  (3,"3392.39","1996-07-24"),
  (3,"4106.40","1959-02-24"),
  (4,"583.74","1970-04-24"),
  (2,"5331.26","1962-06-15"),
  (5,"6720.42","1994-06-22"),
  (5,"5764.81","1995-04-13"),
  (4,"3213.17","2013-07-28"),
  (4,"2061.68","1970-11-01");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (2,"596.98","1951-04-04"),
  (1,"3191.46","1961-05-03"),
  (3,"1618.03","1964-07-08"),
  (4,"4077.70","2021-07-09"),
  (4,"4761.87","1987-10-15"),
  (2,"2966.62","1980-09-29"),
  (2,"4560.74","1951-05-04"),
  (4,"2799.08","1959-06-28"),
  (5,"5206.53","1953-06-18"),
  (5,"3308.82","1943-03-28");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (4,"4437.97","2013-07-10"),
  (2,"1559.07","1976-05-22"),
  (5,"589.23","1958-01-28"),
  (3,"6408.33","1954-09-24"),
  (3,"2158.27","1975-03-11"),
  (2,"6126.13","1945-09-15"),
  (3,"2820.52","1982-10-05"),
  (4,"6076.31","2024-10-19"),
  (3,"1444.84","1960-12-17"),
  (2,"374.97","1993-07-26");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (5,"4245.17","1995-03-26"),
  (3,"9956.37","1951-06-30"),
  (4,"8976.22","2020-05-09"),
  (1,"4400.46","1943-09-25"),
  (4,"7789.97","2016-06-01"),
  (3,"4447.73","1988-08-31"),
  (3,"927.74","1951-06-09"),
  (2,"7410.39","1963-11-14"),
  (3,"8585.64","1966-09-01"),
  (1,"7504.85","1976-09-30");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (2,"2265.90","1991-07-16"),
  (5,"8155.25","1985-01-09"),
  (5,"2645.00","1970-06-26"),
  (1,"1514.19","2020-03-27"),
  (5,"402.68","2010-12-19"),
  (1,"3990.15","1980-03-24"),
  (3,"7095.41","1973-12-25"),
  (3,"9914.25","1991-08-18"),
  (5,"9593.76","1952-03-14"),
  (3,"7321.54","1953-05-03");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (2,"4302.46","1966-10-24"),
  (3,"4403.59","1994-08-16"),
  (3,"6016.17","1946-05-04"),
  (3,"6533.64","2000-04-17"),
  (5,"9636.07","2005-12-29"),
  (3,"6724.62","1997-04-10"),
  (1,"1233.43","2022-07-22"),
  (4,"6694.70","2009-08-03"),
  (4,"3342.00","1973-10-13"),
  (3,"5926.41","2015-09-14");
INSERT INTO `account` (`type_id`,`balance`,`open_date`)
VALUES
  (2,"1620.76","1955-08-27"),
  (5,"5335.94","1955-03-31"),
  (4,"8176.94","1962-10-10"),
  (5,"6653.79","1976-11-07"),
  (3,"8808.08","2009-08-01"),
  (5,"2719.33","2006-06-01"),
  (3,"5147.74","1984-11-29"),
  (3,"8127.08","1948-10-23"),
  (5,"1965.12","2010-07-12"),
  (4,"7352.38","2010-12-21");
 
INSERT INTO `relationship_role` (`role_desc`, `role_desc_abbrv`)
VALUES
	("Sole Owner", "SOL"),
	("Co-Owner First", "COF"),
	("Co-Owner Other", "COO"),
	("Third Party Signatory", "TPS"),
	("Power of Attorney", "POA");

INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (40,195,4),
  (49,149,3),
  (5,27,4),
  (82,140,4),
  (73,49,1),
  (57,85,4),
  (99,129,3),
  (48,18,2),
  (6,173,5),
  (29,128,3);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (20,189,3),
  (50,131,5),
  (94,117,2),
  (86,84,4),
  (96,59,2),
  (15,3,3),
  (9,151,2),
  (59,89,3),
  (16,160,5),
  (25,119,3);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (95,85,4),
  (72,60,4),
  (49,61,1),
  (15,89,2),
  (80,173,2),
  (15,25,4),
  (64,128,1),
  (73,41,5),
  (21,188,5),
  (46,170,2);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (72,37,2),
  (61,107,1),
  (11,177,4),
  (74,72,4),
  (94,94,3),
  (88,31,1),
  (4,179,3),
  (68,166,1),
  (17,9,2),
  (40,186,4);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (55,46,2),
  (5,43,3),
  (81,181,3),
  (42,146,3),
  (29,46,4),
  (84,194,5),
  (58,127,2),
  (5,140,3),
  (95,107,4),
  (6,161,4);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (56,151,4),
  (68,114,3),
  (88,98,4),
  (90,134,4),
  (4,94,2),
  (35,67,4),
  (89,185,2),
  (62,41,3),
  (80,18,3),
  (83,139,3);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (66,188,4),
  (22,6,4),
  (72,193,4),
  (95,9,2),
  (88,12,5),
  (4,33,1),
  (77,87,3),
  (20,56,2),
  (64,145,3),
  (59,71,2);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (69,93,5),
  (75,99,4),
  (43,125,1),
  (62,124,3),
  (23,182,1),
  (2,155,4),
  (76,29,1),
  (31,120,3),
  (41,62,4),
  (18,175,3);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (66,199,2),
  (22,31,3),
  (61,106,3),
  (1,65,2),
  (47,151,1),
  (55,147,2),
  (77,199,1),
  (62,175,5),
  (24,74,1),
  (18,73,5);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (63,140,3),
  (72,67,2),
  (83,66,3),
  (48,39,2),
  (14,163,5),
  (10,192,4),
  (68,181,3),
  (37,178,4),
  (74,15,3),
  (7,34,4);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (62,52,1),
  (56,187,3),
  (25,192,4),
  (79,54,3),
  (58,56,3),
  (23,102,3),
  (23,85,4),
  (20,147,2),
  (93,32,4),
  (29,189,4);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (2,174,4),
  (70,17,5),
  (7,59,5),
  (38,7,5),
  (97,118,5),
  (98,173,5),
  (43,100,1),
  (99,46,4),
  (9,121,5),
  (55,67,2);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (74,51,5),
  (40,167,3),
  (47,106,3),
  (59,48,1),
  (22,48,2),
  (44,33,1),
  (10,151,2),
  (78,73,1),
  (29,188,2),
  (97,37,3);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (24,182,3),
  (23,199,5),
  (39,9,4),
  (52,103,2),
  (36,5,2),
  (78,113,1),
  (91,58,5),
  (45,174,4),
  (94,7,2),
  (7,161,2);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (20,148,2),
  (26,142,5),
  (97,123,2),
  (91,48,5),
  (88,105,1),
  (86,21,2),
  (80,161,4),
  (35,167,2),
  (42,177,5),
  (60,121,2);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (96,157,4),
  (54,34,1),
  (27,151,4),
  (15,156,4),
  (24,174,2),
  (5,149,2),
  (55,181,3),
  (6,79,3),
  (47,190,1),
  (21,114,1);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (14,22,2),
  (98,180,2),
  (1,164,2),
  (44,186,3),
  (36,44,3),
  (15,28,1),
  (88,111,2),
  (92,38,3),
  (29,177,1),
  (29,60,3);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (71,99,1),
  (97,160,4),
  (63,69,3),
  (5,15,4),
  (3,127,1),
  (19,178,4),
  (11,75,3),
  (78,194,4),
  (71,128,3),
  (93,32,2);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (70,99,5),
  (40,116,1),
  (75,193,5),
  (72,178,1),
  (57,50,4),
  (98,70,2),
  (95,30,3),
  (3,45,5),
  (40,76,1),
  (33,37,5);
INSERT INTO `relationship` (`customer_id`,`account_id`,`role_id`)
VALUES
  (17,27,5),
  (5,156,1),
  (94,138,3),
  (34,128,5),
  (42,50,4),
  (20,113,2),
  (9,169,3),
  (68,119,4),
  (80,62,1),
  (24,80,4);
