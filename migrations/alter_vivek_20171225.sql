alter table fields add column iscomment INT(11) DEFAULT 0;
ALTER TABLE `eval` ADD `startdate` DATE NOT NULL AFTER `instructions`;
ALTER TABLE `eval` CHANGE `startdate` `startdate` DATE NULL;

