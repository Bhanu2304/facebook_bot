/*
SQLyog Community v12.4.3 (64 bit)
MySQL - 8.0.33-0ubuntu0.20.04.2 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `dialdee_fbconfigmaster` (
	`id` int (11),
	`client_id` varchar (90),
	`app_id` varchar (3000),
	`client_secret` varchar (3000),
	`fb_id` varchar (1500),
	`page_id` varchar (1500),
	`page_access_token` varchar (3000),
	`user_access_token` varchar (3000),
	`plateform` varchar (3000),
	`interaction_type` varchar (300),
	`help_1` varchar (3000),
	`help_2` varchar (3000),
	`help_3` varchar (3000),
	`active` varchar (3),
	`last_post_read_time` datetime ,
	`last_conv_read_time` datetime ,
	`bot_enable` varchar (6),
	`created_at` datetime ,
	`created_by` varchar (30),
	`updated_at` datetime ,
	`updated_by` varchar (30)
); 
insert into `dialdee_fbconfigmaster` (`id`, `client_id`, `app_id`, `client_secret`, `fb_id`, `page_id`, `page_access_token`, `user_access_token`, `plateform`, `interaction_type`, `help_1`, `help_2`, `help_3`, `active`, `last_post_read_time`, `last_conv_read_time`, `bot_enable`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('7','516','199925049683840','5cda06aed131470337695983d4ab912d',NULL,'105053342632496','EAAC11K2Rb4ABAKJugr3T3nobl29ic4OxT78XGrvSaOFVlo10AaoK71oHwYRJD9QSWXZBABRLNZA8aCaYJdwRPtBdADBiVlLuhg9uVMPP8nSoAOhoZB8bMffKLK5ZByA5fWJY5HyjZCamOMrXZALWZCZA7vzRBL1RrIBzuwKstQHAAs4yPVasrm65yWxlE28KkEuoi5iZC4s4pXKdfza5twJqO',NULL,'MESSENGER','Facebook',NULL,NULL,NULL,'1','2023-06-23 17:55:50','2023-07-27 16:22:04','1','2023-06-23 17:38:01','1',NULL,NULL);
insert into `dialdee_fbconfigmaster` (`id`, `client_id`, `app_id`, `client_secret`, `fb_id`, `page_id`, `page_access_token`, `user_access_token`, `plateform`, `interaction_type`, `help_1`, `help_2`, `help_3`, `active`, `last_post_read_time`, `last_conv_read_time`, `bot_enable`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('8','516','199925049683840','5cda06aed131470337695983d4ab912d',NULL,'105053342632496','EAAC11K2Rb4ABAKJugr3T3nobl29ic4OxT78XGrvSaOFVlo10AaoK71oHwYRJD9QSWXZBABRLNZA8aCaYJdwRPtBdADBiVlLuhg9uVMPP8nSoAOhoZB8bMffKLK5ZByA5fWJY5HyjZCamOMrXZALWZCZA7vzRBL1RrIBzuwKstQHAAs4yPVasrm65yWxlE28KkEuoi5iZC4s4pXKdfza5twJqO',NULL,'INSTAGRAM','Instagram',NULL,NULL,NULL,'1','2023-06-23 17:39:14','2023-07-27 16:22:05',NULL,'2023-06-23 17:39:21','1',NULL,NULL);
insert into `dialdee_fbconfigmaster` (`id`, `client_id`, `app_id`, `client_secret`, `fb_id`, `page_id`, `page_access_token`, `user_access_token`, `plateform`, `interaction_type`, `help_1`, `help_2`, `help_3`, `active`, `last_post_read_time`, `last_conv_read_time`, `bot_enable`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('9','301','706160150966404','0ee76d65d41bffdc17859c79d11435b9',NULL,'932156103594326','EAAKCP7jz8IQBAOaiymvxZCn25EVGPEfk4ZCppWM8JcvLZCzzNNrIOvAsdwsXBKK5GJ04E04Nlh1fstbpM1jPOtAkEpuMlxWZC6Tr10ZB5qAmN32aADM7h2bn9bQLkNWKK3vd3abIb0dWSzSbYAZAdNvk6bNRWZBDmPiEb0qIA2NbtwXoaoJzBtb',NULL,'MESSENGER','Facebook',NULL,NULL,NULL,'1',NULL,NULL,'1','2023-07-25 13:55:58','1',NULL,NULL);
