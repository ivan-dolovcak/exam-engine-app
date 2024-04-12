use `exam_engine`;

alter table `User`
modify column `lastLoginTime` datetime not null default utc_timestamp();
