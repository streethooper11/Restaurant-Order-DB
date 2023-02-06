-- MySQL dump 10.13  Distrib 8.0.27, for macos11 (x86_64)
--
-- Host: 127.0.0.1    Database: 471db
-- ------------------------------------------------------
-- Server version	8.0.27

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `471db`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `471db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `471db`;

--
-- Table structure for table `Account`
--

DROP TABLE IF EXISTS `Account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Account` (
  `Email_ID` varchar(45) NOT NULL,
  `Password` varchar(45) DEFAULT NULL,
  `FName` varchar(45) DEFAULT NULL,
  `LName` varchar(45) DEFAULT NULL,
  `Type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Email_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Account`
--

LOCK TABLES `Account` WRITE;
/*!40000 ALTER TABLE `Account` DISABLE KEYS */;
INSERT INTO `Account` VALUES ('bakechef@gmail.com','yummy','Boss','Man','Admin'),('carlsjr@gmail.com','yummy','Boss','Carlsjr','Admin'),('connor@gmail.com','123','Connor','McDavid','User'),('hi@gmail.com','yummy','A','W','Admin'),('jarome@gmail.com','123','Jarome','Iginla','User'),('koreanbbq@gmail.com','yummy','Boss','Man','Admin'),('Kyrie@gmail.com','1234','Kyrie','Irving','User'),('lamelo@gmail.com','1234','Lamelo','Ball','User'),('lonzo@gmail.com','123','Lonzo','Ball','User'),('test@gmail.com','hello','Daniel','Caesar','User'),('william@gmail.com','123','William','Ho','User');
/*!40000 ALTER TABLE `Account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Allergy`
--

DROP TABLE IF EXISTS `Allergy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Allergy` (
  `Name` varchar(45) NOT NULL,
  `Email_ID` varchar(45) NOT NULL,
  `Allergy_Name` varchar(45) NOT NULL,
  PRIMARY KEY (`Name`,`Email_ID`,`Allergy_Name`),
  KEY `Email_ID` (`Email_ID`),
  CONSTRAINT `allergy_ibfk_1` FOREIGN KEY (`Name`) REFERENCES `Profile` (`Name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `allergy_ibfk_2` FOREIGN KEY (`Email_ID`) REFERENCES `Profile` (`Email_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Allergy`
--

LOCK TABLES `Allergy` WRITE;
/*!40000 ALTER TABLE `Allergy` DISABLE KEYS */;
INSERT INTO `Allergy` VALUES ('Lonzo Ball','lonzo@gmail.com','meat');
/*!40000 ALTER TABLE `Allergy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Consists_of`
--

DROP TABLE IF EXISTS `Consists_of`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Consists_of` (
  `Order_ID` int NOT NULL,
  `Dish_ID` int NOT NULL,
  `Quantity` int DEFAULT NULL,
  PRIMARY KEY (`Order_ID`,`Dish_ID`),
  KEY `Dish_ID` (`Dish_ID`),
  CONSTRAINT `consists_of_ibfk_1` FOREIGN KEY (`Order_ID`) REFERENCES `Order` (`Order_ID`) ON DELETE CASCADE,
  CONSTRAINT `consists_of_ibfk_2` FOREIGN KEY (`Dish_ID`) REFERENCES `Dish` (`Dish_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Consists_of`
--

LOCK TABLES `Consists_of` WRITE;
/*!40000 ALTER TABLE `Consists_of` DISABLE KEYS */;
/*!40000 ALTER TABLE `Consists_of` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Dish`
--

DROP TABLE IF EXISTS `Dish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Dish` (
  `Dish_ID` int NOT NULL AUTO_INCREMENT,
  `Dish_Name` varchar(45) NOT NULL,
  `Price` decimal(5,2) DEFAULT NULL,
  `Category` varchar(45) DEFAULT NULL,
  `RestaurantID` int NOT NULL,
  PRIMARY KEY (`Dish_ID`),
  KEY `RestaurantID` (`RestaurantID`),
  CONSTRAINT `dish_ibfk_1` FOREIGN KEY (`RestaurantID`) REFERENCES `Restaurant` (`R_ID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Dish`
--

LOCK TABLES `Dish` WRITE;
/*!40000 ALTER TABLE `Dish` DISABLE KEYS */;
INSERT INTO `Dish` VALUES (1,'BBQ Beef',12.00,'Meat',1),(2,'BBQ Chicken',15.00,'Meat',1);
/*!40000 ALTER TABLE `Dish` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Has`
--

DROP TABLE IF EXISTS `Has`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Has` (
  `Menu_ID` int NOT NULL,
  `Dish_ID` int NOT NULL,
  PRIMARY KEY (`Menu_ID`,`Dish_ID`),
  KEY `Dish_ID` (`Dish_ID`),
  CONSTRAINT `has_ibfk_1` FOREIGN KEY (`Menu_ID`) REFERENCES `Menu` (`Menu_ID`) ON DELETE CASCADE,
  CONSTRAINT `has_ibfk_2` FOREIGN KEY (`Dish_ID`) REFERENCES `Dish` (`Dish_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Has`
--

LOCK TABLES `Has` WRITE;
/*!40000 ALTER TABLE `Has` DISABLE KEYS */;
/*!40000 ALTER TABLE `Has` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `History`
--

DROP TABLE IF EXISTS `History`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `History` (
  `History_ID` int NOT NULL AUTO_INCREMENT,
  `Order_Place` varchar(45) DEFAULT NULL,
  `Total_Price` decimal(5,2) DEFAULT NULL,
  `UserEmail_ID` varchar(45) DEFAULT NULL,
  `Order_ID` int DEFAULT NULL,
  PRIMARY KEY (`History_ID`),
  KEY `UserEmail_ID` (`UserEmail_ID`),
  KEY `Order_ID` (`Order_ID`),
  CONSTRAINT `history_ibfk_1` FOREIGN KEY (`UserEmail_ID`) REFERENCES `Account` (`Email_ID`) ON DELETE CASCADE,
  CONSTRAINT `history_ibfk_2` FOREIGN KEY (`Order_ID`) REFERENCES `Order` (`Order_ID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `History`
--

LOCK TABLES `History` WRITE;
/*!40000 ALTER TABLE `History` DISABLE KEYS */;
INSERT INTO `History` VALUES (1,'koreanbbq',13.00,'lonzo@gmail.com',1);
/*!40000 ALTER TABLE `History` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Ingredient`
--

DROP TABLE IF EXISTS `Ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Ingredient` (
  `IngredientID` int NOT NULL AUTO_INCREMENT,
  `Type` varchar(45) DEFAULT NULL,
  `IngredientName` varchar(45) NOT NULL,
  PRIMARY KEY (`IngredientID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Ingredient`
--

LOCK TABLES `Ingredient` WRITE;
/*!40000 ALTER TABLE `Ingredient` DISABLE KEYS */;
INSERT INTO `Ingredient` VALUES (1,'meat','beef'),(2,'meat','chicken');
/*!40000 ALTER TABLE `Ingredient` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Menu`
--

DROP TABLE IF EXISTS `Menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Menu` (
  `Menu_ID` int NOT NULL AUTO_INCREMENT,
  `Menu_Name` varchar(45) DEFAULT NULL,
  `RestaurantID` int NOT NULL,
  PRIMARY KEY (`Menu_ID`),
  KEY `Menu_ID_idx` (`Menu_ID`),
  KEY `RestaurantID` (`RestaurantID`),
  CONSTRAINT `RestaurantID` FOREIGN KEY (`RestaurantID`) REFERENCES `Restaurant` (`R_ID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Menu`
--

LOCK TABLES `Menu` WRITE;
/*!40000 ALTER TABLE `Menu` DISABLE KEYS */;
INSERT INTO `Menu` VALUES (1,'Lunch',1),(2,'Lunch',1);
/*!40000 ALTER TABLE `Menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Needs`
--

DROP TABLE IF EXISTS `Needs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Needs` (
  `Dish_ID` int NOT NULL,
  `IngredientID` int NOT NULL,
  `Amount_Needed` int DEFAULT NULL,
  PRIMARY KEY (`Dish_ID`,`IngredientID`),
  KEY `IngredientID` (`IngredientID`),
  CONSTRAINT `needs_ibfk_1` FOREIGN KEY (`Dish_ID`) REFERENCES `Dish` (`Dish_ID`) ON DELETE CASCADE,
  CONSTRAINT `needs_ibfk_2` FOREIGN KEY (`IngredientID`) REFERENCES `Ingredient` (`IngredientID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Needs`
--

LOCK TABLES `Needs` WRITE;
/*!40000 ALTER TABLE `Needs` DISABLE KEYS */;
INSERT INTO `Needs` VALUES (1,1,1),(2,2,1);
/*!40000 ALTER TABLE `Needs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Order`
--

DROP TABLE IF EXISTS `Order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Order` (
  `Order_ID` int NOT NULL AUTO_INCREMENT,
  `Order_Time` datetime DEFAULT NULL,
  `Total_Price` decimal(5,2) DEFAULT NULL,
  `Email_ID` varchar(45) DEFAULT NULL,
  `Profile_Name` varchar(45) DEFAULT NULL,
  `RestaurantID` int DEFAULT NULL,
  PRIMARY KEY (`Order_ID`),
  KEY `Email_ID` (`Email_ID`),
  KEY `Profile_Name` (`Profile_Name`),
  KEY `RestaurantID` (`RestaurantID`),
  CONSTRAINT `order_ibfk_1` FOREIGN KEY (`Email_ID`) REFERENCES `Profile` (`Email_ID`) ON DELETE CASCADE,
  CONSTRAINT `order_ibfk_2` FOREIGN KEY (`Profile_Name`) REFERENCES `Profile` (`Name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_ibfk_3` FOREIGN KEY (`RestaurantID`) REFERENCES `Restaurant` (`R_ID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Order`
--

LOCK TABLES `Order` WRITE;
/*!40000 ALTER TABLE `Order` DISABLE KEYS */;
INSERT INTO `Order` VALUES (1,'1000-01-01 00:00:00',13.00,'lonzo@gmail.com','Lonzo Ball',1),(2,'9999-12-31 23:59:59',14.00,'lonzo@gmail.com','Lonzo Ball',1);
/*!40000 ALTER TABLE `Order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Profile`
--

DROP TABLE IF EXISTS `Profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Profile` (
  `Email_ID` varchar(45) NOT NULL,
  `Name` varchar(45) NOT NULL,
  PRIMARY KEY (`Name`,`Email_ID`),
  KEY `Email_ID` (`Email_ID`),
  CONSTRAINT `Email_ID` FOREIGN KEY (`Email_ID`) REFERENCES `Account` (`Email_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Profile`
--

LOCK TABLES `Profile` WRITE;
/*!40000 ALTER TABLE `Profile` DISABLE KEYS */;
INSERT INTO `Profile` VALUES ('lonzo@gmail.com','Lonzo Ball');
/*!40000 ALTER TABLE `Profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Restaurant`
--

DROP TABLE IF EXISTS `Restaurant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Restaurant` (
  `R_ID` int NOT NULL AUTO_INCREMENT,
  `AdminEmail_ID` varchar(45) DEFAULT NULL,
  `Location` varchar(45) DEFAULT NULL,
  `Name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`R_ID`),
  KEY `AdminEmail_ID` (`AdminEmail_ID`),
  CONSTRAINT `AdminEmail_ID` FOREIGN KEY (`AdminEmail_ID`) REFERENCES `Account` (`Email_ID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Restaurant`
--

LOCK TABLES `Restaurant` WRITE;
/*!40000 ALTER TABLE `Restaurant` DISABLE KEYS */;
INSERT INTO `Restaurant` VALUES (1,'koreanbbq@gmail.com','Calgary','Korean BBQ'),(2,'bakechef@gmail.com','Calgary','Bake Chef'),(3,'carlsjr@gmail.com','Calgary','Carls Jr'),(4,'hi@gmail.com','Canada','AW');
/*!40000 ALTER TABLE `Restaurant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Restaurant_has_Ingredient`
--

DROP TABLE IF EXISTS `Restaurant_has_Ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Restaurant_has_Ingredient` (
  `IngredientID` int NOT NULL,
  `RestaurantID` int NOT NULL,
  `Quantity` int NOT NULL,
  PRIMARY KEY (`IngredientID`,`RestaurantID`),
  KEY `RestaurantID` (`RestaurantID`),
  CONSTRAINT `restaurant_has_ingredient_ibfk_1` FOREIGN KEY (`IngredientID`) REFERENCES `Ingredient` (`IngredientID`) ON DELETE CASCADE,
  CONSTRAINT `restaurant_has_ingredient_ibfk_2` FOREIGN KEY (`RestaurantID`) REFERENCES `Restaurant` (`R_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Restaurant_has_Ingredient`
--

LOCK TABLES `Restaurant_has_Ingredient` WRITE;
/*!40000 ALTER TABLE `Restaurant_has_Ingredient` DISABLE KEYS */;
INSERT INTO `Restaurant_has_Ingredient` VALUES (1,1,50),(2,1,40);
/*!40000 ALTER TABLE `Restaurant_has_Ingredient` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Review`
--

DROP TABLE IF EXISTS `Review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Review` (
  `Rating` int DEFAULT NULL,
  `Comment` varchar(255) DEFAULT NULL,
  `UserEmail_ID` varchar(45) NOT NULL,
  `Date_Time` datetime NOT NULL,
  `Reply` varchar(255) DEFAULT NULL,
  `AdminEmail_ID` varchar(45) DEFAULT NULL,
  `Order_ID` int DEFAULT NULL,
  PRIMARY KEY (`UserEmail_ID`,`Date_Time`),
  KEY `Review_Date_idx` (`Date_Time`),
  KEY `AdminEmail_ID` (`AdminEmail_ID`),
  KEY `Order_ID` (`Order_ID`),
  CONSTRAINT `review_ibfk_1` FOREIGN KEY (`UserEmail_ID`) REFERENCES `Account` (`Email_ID`) ON DELETE CASCADE,
  CONSTRAINT `review_ibfk_2` FOREIGN KEY (`AdminEmail_ID`) REFERENCES `Account` (`Email_ID`) ON DELETE SET NULL,
  CONSTRAINT `review_ibfk_3` FOREIGN KEY (`Order_ID`) REFERENCES `Order` (`Order_ID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Review`
--

LOCK TABLES `Review` WRITE;
/*!40000 ALTER TABLE `Review` DISABLE KEYS */;
INSERT INTO `Review` VALUES (5,'yummy','lonzo@gmail.com','1000-01-01 00:00:00',NULL,'koreanbbq@gmail.com',1),(5,'Nice!','lonzo@gmail.com','9999-12-31 23:58:59','','bakechef@gmail.com',2);
/*!40000 ALTER TABLE `Review` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-12-16 14:23:14
