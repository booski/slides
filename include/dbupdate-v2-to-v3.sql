alter table `show_slide` add column `starttime` varchar(64);
alter table `show_slide` add column `autodelete` bool not null default false;
