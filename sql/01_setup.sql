-- Note: the identifiers on the production server are different.
create user 'exam_engine_admin'@'localhost' identified by 'admin';
create database `exam_engine`;
grant all privileges on `exam_engine`.* to 'exam_engine_admin'@'localhost';
