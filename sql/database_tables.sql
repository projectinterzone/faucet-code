
CREATE TABLE `payments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `coin_type` int(11) unsigned NOT NULL DEFAULT '0',
  `coin_address` varchar(50) NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `state` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `browser` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `payout_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `coin_type` int(10) NOT NULL DEFAULT '0',
  `number_of_payees` int(10) NOT NULL DEFAULT '0',
  `total_sent` decimal(16,8) NOT NULL DEFAULT '0.00000000',
  `time_sent` int(10) NOT NULL DEFAULT '0',
  `transaction_id` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
