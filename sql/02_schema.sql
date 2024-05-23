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

create table `Document` (
    `ID`                  mediumint unsigned not null auto_increment,
    `name`                varchar(50) not null,
    `type`                enum("exam", "form") not null,
    `visibility`          enum("public", "unlisted", "private") not null,
    `numMaxSubmissions`   tinyint unsigned,
    `authorID`            mediumint unsigned not null,
    `deadlineDatetime`    datetime,
    `creationDate`        date not null default utc_date(),
    `documentJSON`        json,
    `solutionJSON`        json,
    primary key (`ID`),
    constraint `FK_author`
        foreign key (`authorID`) references `User`(`ID`)
        on delete cascade
);

create table `Submission` (
    `ID`                  mediumint unsigned not null auto_increment,
    `documentID`          mediumint unsigned not null,
    `userID`              mediumint unsigned not null,
    `creationDate`        datetime not null default utc_timestamp(),
    `submissionJSON`      json not null,
    primary key (`ID`),
    constraint `FK_SubmissionDocument`
        foreign key (`documentID`) references `Document`(`ID`)
        on delete cascade,
    constraint `FK_SubmissionAuthor`
        foreign key (`userID`) references `User`(`ID`)
        on delete cascade
);
