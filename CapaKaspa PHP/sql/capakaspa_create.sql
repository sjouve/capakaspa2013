-- phpMyAdmin SQL Dump
-- version 2.6.4-pl3
-- http://www.phpmyadmin.net
-- 
-- Serveur: db496.1and1.fr
-- Généré le : Dimanche 25 Novembre 2012 à 21:04
-- Version du serveur: 4.0.27
-- Version de PHP: 5.3.3-7+squeeze14
-- 
-- Base de données: `db151183899`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `eco`
-- 

CREATE TABLE `eco` (
  `eco` char(3) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`eco`)
) ;

-- --------------------------------------------------------

-- 
-- Structure de la table `elo_history`
-- 

CREATE TABLE `elo_history` (
  `eloDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `elo` smallint(6) NOT NULL default '0',
  `playerID` int(11) NOT NULL default '0'
) ;

-- --------------------------------------------------------

-- 
-- Structure de la table `fav_players`
-- 

CREATE TABLE `fav_players` (
  `favoriteID` int(11) NOT NULL auto_increment,
  `playerID` int(11) NOT NULL default '0',
  `favPlayerID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`favoriteID`),
  UNIQUE KEY `FAV_UNIQUE` (`playerID`,`favPlayerID`)
)  AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `fen_eco`
-- 

CREATE TABLE `fen_eco` (
  `fen` char(64) NOT NULL default '',
  `eco` char(3) NOT NULL default '',
  `trait` char(1) NOT NULL default '',
  PRIMARY KEY  (`fen`)
) ;

-- --------------------------------------------------------

-- 
-- Structure de la table `games`
-- 

CREATE TABLE `games` (
  `gameID` smallint(6) NOT NULL auto_increment,
  `whitePlayer` mediumint(9) NOT NULL default '0',
  `blackPlayer` mediumint(9) NOT NULL default '0',
  `gameMessage` enum('playerInvited','inviteDeclined','draw','playerResigned','checkMate') default NULL,
  `messageFrom` enum('black','white') default NULL,
  `dateCreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastMove` datetime NOT NULL default '0000-00-00 00:00:00',
  `dialogue` longtext,
  `position` varchar(64) default NULL,
  `eco` char(3) default NULL,
  `type` tinyint(4) NOT NULL default '0',
  `flagBishop` tinyint(4) NOT NULL default '0',
  `flagKnight` tinyint(4) NOT NULL default '0',
  `flagRook` tinyint(4) NOT NULL default '0',
  `flagQueen` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`gameID`)
)  AUTO_INCREMENT=2965 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `history`
-- 

CREATE TABLE `history` (
  `timeOfMove` datetime NOT NULL default '0000-00-00 00:00:00',
  `gameID` smallint(6) NOT NULL default '0',
  `curPiece` enum('pawn','bishop','knight','rook','queen','king') NOT NULL default 'pawn',
  `curColor` enum('white','black') NOT NULL default 'white',
  `fromRow` smallint(6) NOT NULL default '0',
  `fromCol` smallint(6) NOT NULL default '0',
  `toRow` smallint(6) NOT NULL default '0',
  `toCol` smallint(6) NOT NULL default '0',
  `replaced` enum('pawn','bishop','knight','rook','queen','king') default NULL,
  `promotedTo` enum('pawn','bishop','knight','rook','queen','king') default NULL,
  `isInCheck` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`timeOfMove`,`gameID`)
) ;

-- --------------------------------------------------------

-- 
-- Structure de la table `messages`
-- 

CREATE TABLE `messages` (
  `msgID` int(11) NOT NULL auto_increment,
  `gameID` smallint(6) NOT NULL default '0',
  `msgType` enum('undo','draw') NOT NULL default 'undo',
  `msgStatus` enum('request','approved','denied') NOT NULL default 'request',
  `destination` enum('black','white') NOT NULL default 'black',
  PRIMARY KEY  (`msgID`)
)  AUTO_INCREMENT=588 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `online_players`
-- 

CREATE TABLE `online_players` (
  `playerID` int(11) NOT NULL default '0',
  `lastActionTime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`playerID`)
) ;

-- --------------------------------------------------------

-- 
-- Structure de la table `players`
-- 

CREATE TABLE `players` (
  `playerID` int(11) NOT NULL auto_increment,
  `PASSWORD` varchar(16) NOT NULL default '',
  `firstName` varchar(20) NOT NULL default '',
  `lastName` varchar(20) NOT NULL default '',
  `nick` varchar(20) NOT NULL default '',
  `profil` varchar(255) NOT NULL default '',
  `anneeNaissance` varchar(4) NOT NULL default '',
  `situationGeo` varchar(30) default NULL,
  `email` varchar(50) NOT NULL default '',
  `lastConnection` datetime NOT NULL default '0000-00-00 00:00:00',
  `activate` tinyint(4) NOT NULL default '0',
  `elo` smallint(6) NOT NULL default '1300',
  `eloProgress` tinyint(4) NOT NULL default '0',
  `socialNetwork` char(2) default NULL,
  `socialID` varchar(100) default NULL,
  `creationDate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`playerID`),
  UNIQUE KEY `nick` (`nick`)
)  AUTO_INCREMENT=420 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `preferences`
-- 

CREATE TABLE `preferences` (
  `playerID` int(11) NOT NULL default '0',
  `preference` char(20) NOT NULL default '',
  `value` char(50) default NULL,
  PRIMARY KEY  (`playerID`,`preference`)
) ;

-- --------------------------------------------------------

-- 
-- Structure de la table `vacation`
-- 

CREATE TABLE `vacation` (
  `playerID` int(11) NOT NULL default '0',
  `beginDate` date NOT NULL default '0000-00-00',
  `endDate` date NOT NULL default '0000-00-00',
  `duration` int(11) NOT NULL default '0',
  PRIMARY KEY  (`playerID`,`beginDate`)
) ;
