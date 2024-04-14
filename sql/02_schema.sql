use `exam_engine`;

create table `User` (
    `ID`                   mediumint unsigned not null auto_increment,
    `username`             varchar(30) not null,
    `email`                varchar(50) not null,
    `passwordHash`         char(60) not null, -- PHP PASSWORD_BCRYPT
    `firstName`            varchar(40) not null,
    `lastName`             varchar(40) not null,
    `creationDate`         date not null default utc_date(),
    `lastLoginTime`        datetime not null default utc_timestamp(),
    primary key (`ID`),
    constraint `UK_username`    unique key (`username`),
    constraint `UK_email`       unique key (`email`)
);
