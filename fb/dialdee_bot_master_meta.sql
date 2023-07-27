/*
SQLyog Community v12.4.3 (64 bit)
MySQL - 8.0.33-0ubuntu0.20.04.2 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `dialdee_bot_master_meta` (
	`id` int (11),
	`client_id` varchar (300),
	`phoneno_id` varchar (30),
	`bot_id` varchar (30),
	`parent_id` varchar (30),
	`hide` varchar (6),
	`OptionType` varchar (300),
	`OptionName` text ,
	`priority` varchar (6),
	`phase1` varchar (60),
	`created_at` datetime ,
	`created_by` varchar (300),
	`updated_at` datetime ,
	`updated_by` varchar (300)
); 
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('1','301','9','1','0','0','Validate User','Welcome to {brand-name}, I am Dee your virtual assistant powered by Dialdesk','1','1','2023-07-03 11:17:46.000000','1','2023-07-03 11:17:50.000000','1');
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('2','301','9','1','1','0','Deals','list of products','1','1','2023-07-03 11:28:47.000000','1','2023-07-03 11:28:51.000000',NULL);
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('3','301','9','1','1','0','Order','Ordering Process','2','1','2023-07-03 11:32:03.000000','1','2023-07-03 11:32:07.000000',NULL);
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('4','301','9','1','1','0','Menu','Help with my order','3','1','2023-07-03 11:34:18.000000','1','2023-07-03 11:34:22.000000',NULL);
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('5','301','9','1','1','0','Questionnaire','Return/Exchange/Cancellation/Refund','4','1','2023-07-03 11:34:51.000000','1','2023-07-03 11:34:56.000000',NULL);
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('6','301','9','1','3','0','ordering_process','Great! Let me take you to our website {url}. Just add the product to cart and fill necessary\r\ninformation required Choose payment option and click on checkout.','1','0','2023-07-06 12:33:26.000000','1','2023-07-06 12:33:29.000000',NULL);
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('7','301','9','1','5','0','ticket_option','I\'m sorry to hear that.Let me connect you with customer support executive for better assistance. Your ticket id no. is {ticket-no}. One of our executive will get in touch with you soon.','1','Return','2023-07-06 18:33:29.000000','1','2023-07-06 18:33:32.000000',NULL);
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('8','301','9','1','2','0','view_products','View Product','1','0','2023-07-25 19:31:43.000000','1',NULL,NULL);
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('9','301','9','1','2','0','view_products','Can i help you with anything else ?','2','0','2023-07-27 10:01:05.000000','1','2023-07-27 10:01:10.000000',NULL);
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('10','301','9','1','9','0','yes','Yes','1','0','2023-07-27 11:26:06.000000','1',NULL,NULL);
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('11','301','9','1','9','0','no','No','2','0','2023-07-27 11:28:02.000000','1',NULL,NULL);
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('12','301','9','1','10','0','ticket_option','Let me connect you to customer support executive for better assistant. your ticket id no. is {ticket-no}. some of our executive will get in touch with you soon.','1','Assistant','2023-07-27 11:36:40.000000','1',NULL,NULL);
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('13','301','9','1','11','0','feedback','Thank you for visiting our {brand-name} Store. Have a good day ahead!','1','0','2023-07-27 11:37:36.000000','1',NULL,NULL);
insert into `dialdee_bot_master_meta` (`id`, `client_id`, `phoneno_id`, `bot_id`, `parent_id`, `hide`, `OptionType`, `OptionName`, `priority`, `phase1`, `created_at`, `created_by`, `updated_at`, `updated_by`) values('14','301','9','1','4','0','chat_to_agent','Chat To Agent','1','0','2023-07-27 14:55:24.000000','1',NULL,NULL);
