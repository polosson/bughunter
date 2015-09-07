-- phpMyAdmin SQL Dump
-- Généré le: Lun 07 Septembre 2015 à 18:08

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `SaAM_bughunter`
--

CREATE TABLE IF NOT EXISTS `t_bugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `author` varchar(45) NOT NULL,
  `app_url` varchar(250) NOT NULL,
  `app_version` varchar(45) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `img` text,
  `status` varchar(45) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `closed` tinyint(4) DEFAULT NULL,
  `duplicate` int(11) DEFAULT NULL,
  `last_action` datetime DEFAULT NULL,
  `FK_label_ID` int(11) DEFAULT NULL,
  `FK_dev_ID` int(11) DEFAULT NULL,
  `FK_comment_ID` varchar(100) DEFAULT '[]',
  PRIMARY KEY (`id`),
  KEY `label_ID` (`FK_label_ID`,`FK_dev_ID`),
  KEY `FK_label_ID` (`FK_label_ID`,`FK_dev_ID`),
  KEY `FK_dev_ID` (`FK_dev_ID`),
  KEY `FK_comment_ID` (`FK_comment_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `t_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `message` text NOT NULL,
  `FK_dev_ID` int(11) NOT NULL,
  `FK_bug_ID` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dev_ID` (`FK_dev_ID`,`FK_bug_ID`),
  KEY `bug_ID` (`FK_bug_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `t_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(128) NOT NULL,
  `value` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `t_config` (`id`, `nom`, `value`) VALUES
(1, 'password_access', '0a7eb6c98599cb3fe6002f2895fba360'),
(2, 'api_access', '462adc7f9e51ad62f62ff914a61d6a01e0c8c484bc56321227a37924f3a56aa0'),
(3, 'project_name', 'Your project'),
(4, 'git_repo', 'git://your/git/repo/url.git'),
(5, 'project_type', 'open-source');


CREATE TABLE IF NOT EXISTS `t_devs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(45) DEFAULT NULL,
  `mail` varchar(45) DEFAULT NULL,
  `last_action` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail_UNIQUE` (`mail`),
  UNIQUE KEY `pseudo_UNIQUE` (`pseudo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `t_devs` (`id`, `pseudo`, `mail`, `last_action`) VALUES
(-1, 'SaAM team', 'contact@saamanager.net', NULL),
(0, 'none', NULL, NULL);


CREATE TABLE IF NOT EXISTS `t_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `color` varchar(45) NOT NULL,
  `icon` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  UNIQUE KEY `color_UNIQUE` (`color`),
  UNIQUE KEY `icon_UNIQUE` (`icon`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `t_labels` (`id`, `name`, `color`, `icon`) VALUES
(0, 'none', '#dddddd', NULL),
(1, 'bug', '#ff0000', NULL),
(2, 'question', '#8800ff', NULL);
