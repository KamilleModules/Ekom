-- MySQL Script generated by MySQL Workbench
-- Mon Mar 19 21:54:02 2018
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema kamille
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema kamille
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `kamille` DEFAULT CHARACTER SET utf8 ;
USE `kamille` ;

-- -----------------------------------------------------
-- Table `kamille`.`ek_shop_configuration`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_shop_configuration` (
  `key` VARCHAR(64) NOT NULL,
  `value` VARCHAR(256) NOT NULL,
  UNIQUE INDEX `key_UNIQUE` (`key` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_tax_group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_tax_group` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  `label` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_card`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_card` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(128) NOT NULL,
  `description` TEXT NOT NULL,
  `slug` VARCHAR(128) NOT NULL,
  `meta_title` VARCHAR(128) NOT NULL,
  `meta_description` VARCHAR(256) NOT NULL,
  `meta_keywords` TEXT NOT NULL,
  `active` TINYINT(1) NOT NULL,
  `tax_group_id` INT NULL,
  `product_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ek_product_card_ek_tax_group1_idx` (`tax_group_id` ASC),
  INDEX `fk_ek_product_card_ek_product1_idx` (`product_id` ASC),
  CONSTRAINT `fk_ek_product_card_ek_tax_group1`
    FOREIGN KEY (`tax_group_id`)
    REFERENCES `kamille`.`ek_tax_group` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_card_ek_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `kamille`.`ek_product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_seller`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_seller` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_type` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  `bo_active` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_manufacturer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_manufacturer` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product_card_id` INT NOT NULL,
  `seller_id` INT NOT NULL,
  `product_type_id` INT NOT NULL,
  `manufacturer_id` INT NULL,
  `reference` VARCHAR(64) NOT NULL,
  `price` DECIMAL(20,2) NOT NULL,
  `label` VARCHAR(128) NOT NULL,
  `description` TEXT NOT NULL,
  `slug` VARCHAR(128) NOT NULL,
  `meta_title` VARCHAR(128) NOT NULL,
  `meta_description` VARCHAR(256) NOT NULL,
  `meta_keywords` TEXT NOT NULL,
  `wholesale_price` DECIMAL(20,2) NOT NULL,
  `quantity` INT NOT NULL,
  `out_of_stock_text` VARCHAR(128) NOT NULL,
  `active` TINYINT(1) NOT NULL,
  `_discount_badge` VARCHAR(256) NOT NULL,
  `_popularity` DECIMAL(20,2) NOT NULL,
  `codes` TEXT NOT NULL,
  `ean` VARCHAR(64) NOT NULL,
  `height` DECIMAL(20,6) NULL,
  `depth` DECIMAL(20,6) NULL,
  `weight` DECIMAL(20,6) NOT NULL,
  `width` DECIMAL(20,6) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ek_product_ek_product_card1_idx` (`product_card_id` ASC),
  INDEX `fk_ek_product_ek_seller1_idx` (`seller_id` ASC),
  INDEX `fk_ek_product_ek_product_type1_idx` (`product_type_id` ASC),
  INDEX `fk_ek_product_ek_manufacturer1_idx` (`manufacturer_id` ASC),
  CONSTRAINT `fk_ek_product_ek_product_card1`
    FOREIGN KEY (`product_card_id`)
    REFERENCES `kamille`.`ek_product_card` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_ek_seller1`
    FOREIGN KEY (`seller_id`)
    REFERENCES `kamille`.`ek_seller` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_ek_product_type1`
    FOREIGN KEY (`product_type_id`)
    REFERENCES `kamille`.`ek_product_type` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_ek_manufacturer1`
    FOREIGN KEY (`manufacturer_id`)
    REFERENCES `kamille`.`ek_manufacturer` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_attribute`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_attribute` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  `label` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_attribute_value`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_attribute_value` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product_attribute_id` INT NOT NULL,
  `value` VARCHAR(64) NOT NULL,
  `label` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `value_UNIQUE` (`value` ASC),
  INDEX `fk_ek_product_attribute_value_ek_product_attribute1_idx` (`product_attribute_id` ASC),
  CONSTRAINT `fk_ek_product_attribute_value_ek_product_attribute1`
    FOREIGN KEY (`product_attribute_id`)
    REFERENCES `kamille`.`ek_product_attribute` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_has_product_attribute`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_has_product_attribute` (
  `product_id` INT NOT NULL,
  `product_attribute_id` INT NOT NULL,
  `product_attribute_value_id` INT NOT NULL,
  `order` TINYINT(1) NOT NULL,
  PRIMARY KEY (`product_id`, `product_attribute_id`, `product_attribute_value_id`),
  INDEX `fk_product_has_product_attribute_product_attribute1_idx` (`product_attribute_id` ASC),
  INDEX `fk_product_has_product_attribute_product1_idx` (`product_id` ASC),
  INDEX `fk_product_has_product_attribute_product_attribute_value1_idx` (`product_attribute_value_id` ASC),
  CONSTRAINT `fk_product_has_product_attribute_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `kamille`.`ek_product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_product_has_product_attribute_product_attribute1`
    FOREIGN KEY (`product_attribute_id`)
    REFERENCES `kamille`.`ek_product_attribute` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_product_has_product_attribute_product_attribute_value1`
    FOREIGN KEY (`product_attribute_value_id`)
    REFERENCES `kamille`.`ek_product_attribute_value` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_category`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_category` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  `label` VARCHAR(128) NOT NULL,
  `category_id` INT NULL,
  `order` INT NOT NULL,
  `description` TEXT NOT NULL,
  `slug` VARCHAR(128) NOT NULL,
  `meta_title` VARCHAR(128) NOT NULL,
  `meta_description` VARCHAR(256) NOT NULL,
  `meta_keywords` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  INDEX `fk_category_category1_idx` (`category_id` ASC),
  CONSTRAINT `fk_category_category1`
    FOREIGN KEY (`category_id`)
    REFERENCES `kamille`.`ek_category` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_category_has_product_card`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_category_has_product_card` (
  `category_id` INT NOT NULL,
  `product_card_id` INT NOT NULL,
  `order` INT NOT NULL,
  PRIMARY KEY (`category_id`, `product_card_id`),
  INDEX `fk_ek_category_has_ek_product_card_ek_product_card1_idx` (`product_card_id` ASC),
  INDEX `fk_ek_category_has_ek_product_card_ek_category1_idx` (`category_id` ASC),
  CONSTRAINT `fk_ek_category_has_ek_product_card_ek_category1`
    FOREIGN KEY (`category_id`)
    REFERENCES `kamille`.`ek_category` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_category_has_ek_product_card_ek_product_card1`
    FOREIGN KEY (`product_card_id`)
    REFERENCES `kamille`.`ek_product_card` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_tax`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_tax` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `amount` DECIMAL(10,6) NOT NULL,
  `label` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_tax_group_has_tax`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_tax_group_has_tax` (
  `tax_group_id` INT NOT NULL,
  `tax_id` INT NOT NULL,
  `order` TINYINT NOT NULL,
  `mode` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`tax_group_id`, `tax_id`),
  INDEX `fk_tax_group_has_tax_tax1_idx` (`tax_id` ASC),
  INDEX `fk_tax_group_has_tax_tax_group1_idx` (`tax_group_id` ASC),
  CONSTRAINT `fk_tax_group_has_tax_tax_group1`
    FOREIGN KEY (`tax_group_id`)
    REFERENCES `kamille`.`ek_tax_group` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_tax_group_has_tax_tax1`
    FOREIGN KEY (`tax_id`)
    REFERENCES `kamille`.`ek_tax` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(128) NOT NULL,
  `pass` VARCHAR(255) NOT NULL,
  `pseudo` VARCHAR(64) NOT NULL,
  `company` VARCHAR(64) NOT NULL,
  `first_name` VARCHAR(64) NOT NULL,
  `last_name` VARCHAR(64) NOT NULL,
  `date_creation` DATETIME NOT NULL,
  `mobile` VARCHAR(24) NOT NULL,
  `phone` VARCHAR(24) NOT NULL,
  `phone_prefix` VARCHAR(12) NOT NULL,
  `newsletter` TINYINT(1) NOT NULL,
  `gender` TINYINT(1) NOT NULL,
  `birthday` DATE NULL,
  `active_hash` VARCHAR(64) NOT NULL,
  `active` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_discount`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_discount` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(256) NOT NULL,
  `type` VARCHAR(64) NOT NULL,
  `operand` VARCHAR(64) NOT NULL,
  `target` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_user_group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_user_group` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_user_has_user_group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_user_has_user_group` (
  `user_id` INT NOT NULL,
  `user_group_id` INT NOT NULL,
  PRIMARY KEY (`user_id`, `user_group_id`),
  INDEX `fk_ek_user_has_ek_user_group_ek_user_group1_idx` (`user_group_id` ASC),
  INDEX `fk_ek_user_has_ek_user_group_ek_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_ek_user_has_ek_user_group_ek_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `kamille`.`ek_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_user_has_ek_user_group_ek_user_group1`
    FOREIGN KEY (`user_group_id`)
    REFERENCES `kamille`.`ek_user_group` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_has_discount`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_has_discount` (
  `product_id` INT NOT NULL,
  `discount_id` INT NOT NULL,
  `conditions` TEXT NOT NULL,
  `active` TINYINT(1) NOT NULL,
  PRIMARY KEY (`product_id`, `discount_id`),
  INDEX `fk_ek_product_has_ek_discount_ek_discount1_idx` (`discount_id` ASC),
  INDEX `fk_ek_product_has_ek_discount_ek_product1_idx` (`product_id` ASC),
  CONSTRAINT `fk_ek_product_has_ek_discount_ek_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `kamille`.`ek_product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_has_ek_discount_ek_discount1`
    FOREIGN KEY (`discount_id`)
    REFERENCES `kamille`.`ek_discount` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_card_has_discount`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_card_has_discount` (
  `product_card_id` INT NOT NULL,
  `discount_id` INT NOT NULL,
  `conditions` TEXT NOT NULL,
  `active` TINYINT(1) NOT NULL,
  PRIMARY KEY (`product_card_id`, `discount_id`),
  INDEX `fk_ek_product_card_has_ek_discount_ek_discount1_idx` (`discount_id` ASC),
  INDEX `fk_ek_product_card_has_ek_discount_ek_product_card1_idx` (`product_card_id` ASC),
  CONSTRAINT `fk_ek_product_card_has_ek_discount_ek_product_card1`
    FOREIGN KEY (`product_card_id`)
    REFERENCES `kamille`.`ek_product_card` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_card_has_ek_discount_ek_discount1`
    FOREIGN KEY (`discount_id`)
    REFERENCES `kamille`.`ek_discount` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_category_has_discount`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_category_has_discount` (
  `category_id` INT NOT NULL,
  `discount_id` INT NOT NULL,
  `conditions` TEXT NOT NULL,
  `active` TINYINT(1) NOT NULL,
  PRIMARY KEY (`category_id`, `discount_id`),
  INDEX `fk_ek_category_has_ek_discount_ek_discount1_idx` (`discount_id` ASC),
  INDEX `fk_ek_category_has_ek_discount_ek_category1_idx` (`category_id` ASC),
  CONSTRAINT `fk_ek_category_has_ek_discount_ek_category1`
    FOREIGN KEY (`category_id`)
    REFERENCES `kamille`.`ek_category` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_category_has_ek_discount_ek_discount1`
    FOREIGN KEY (`discount_id`)
    REFERENCES `kamille`.`ek_discount` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_order`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_order` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NULL,
  `reference` VARCHAR(64) NOT NULL,
  `date` DATETIME NOT NULL,
  `amount` DECIMAL(20,2) NOT NULL,
  `coupon_saving` DECIMAL(20,2) NOT NULL,
  `cart_quantity` INT NOT NULL,
  `shipping_country_iso_code` VARCHAR(64) NOT NULL,
  `payment_method` VARCHAR(64) NOT NULL,
  `payment_method_extra` VARCHAR(64) NOT NULL,
  `user_info` BLOB NOT NULL,
  `shop_info` BLOB NOT NULL,
  `shipping_address` BLOB NOT NULL,
  `billing_address` BLOB NOT NULL,
  `order_details` MEDIUMBLOB NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ek_order_ek_user1_idx` (`user_id` ASC),
  UNIQUE INDEX `reference_UNIQUE` (`reference` ASC),
  CONSTRAINT `fk_ek_order_ek_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `kamille`.`ek_user` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_order_status`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_order_status` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(64) NOT NULL,
  `color` VARCHAR(32) NOT NULL,
  `bg_color` VARCHAR(32) NOT NULL,
  `label` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `code_UNIQUE` (`code` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_order_has_order_status`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_order_has_order_status` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `order_status_id` INT NOT NULL,
  `date` DATETIME NOT NULL,
  `extra` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ek_order_has_ek_order_status_ek_order_status1_idx` (`order_status_id` ASC),
  INDEX `fk_ek_order_has_ek_order_status_ek_order1_idx` (`order_id` ASC),
  CONSTRAINT `fk_ek_order_has_ek_order_status_ek_order1`
    FOREIGN KEY (`order_id`)
    REFERENCES `kamille`.`ek_order` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_order_has_ek_order_status_ek_order_status1`
    FOREIGN KEY (`order_status_id`)
    REFERENCES `kamille`.`ek_order_status` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_coupon`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_coupon` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(64) NOT NULL,
  `label` VARCHAR(256) NOT NULL,
  `active` TINYINT(1) NOT NULL,
  `procedure_type` VARCHAR(64) NOT NULL,
  `procedure_operand` VARCHAR(128) NOT NULL,
  `target` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `code_UNIQUE` (`code` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_country`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_country` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `iso_code` VARCHAR(64) NOT NULL,
  `label` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `iso_code_UNIQUE` (`iso_code` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_address` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(64) NOT NULL,
  `phone` VARCHAR(64) NOT NULL,
  `phone_prefix` VARCHAR(12) NOT NULL,
  `address` VARCHAR(256) NOT NULL,
  `city` VARCHAR(128) NOT NULL,
  `postcode` VARCHAR(64) NOT NULL,
  `supplement` VARCHAR(128) NOT NULL,
  `active` TINYINT(1) NOT NULL,
  `country_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ek_address_ek_country1_idx` (`country_id` ASC),
  CONSTRAINT `fk_ek_address_ek_country1`
    FOREIGN KEY (`country_id`)
    REFERENCES `kamille`.`ek_country` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_user_has_address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_user_has_address` (
  `user_id` INT NOT NULL,
  `address_id` INT NOT NULL,
  `order` TINYINT(1) NOT NULL,
  `is_default_shipping_address` TINYINT(1) NOT NULL,
  `is_default_billing_address` TINYINT(1) NOT NULL,
  INDEX `fk_ek_user_has_ek_address_ek_address1_idx` (`address_id` ASC),
  INDEX `fk_ek_user_has_ek_address_ek_user1_idx` (`user_id` ASC),
  PRIMARY KEY (`user_id`, `address_id`),
  CONSTRAINT `fk_ek_user_has_ek_address_ek_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `kamille`.`ek_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_user_has_ek_address_ek_address1`
    FOREIGN KEY (`address_id`)
    REFERENCES `kamille`.`ek_address` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_carrier`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_carrier` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  `label` VARCHAR(64) NOT NULL,
  `priority` TINYINT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_payment_method`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_payment_method` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  `label` VARCHAR(64) NOT NULL,
  `configuration` TEXT NOT NULL,
  `order` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_user_has_product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_user_has_product` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_details` TEXT NOT NULL,
  `date` DATETIME NOT NULL,
  `deleted_date` DATETIME NULL,
  INDEX `fk_ek_user_has_ek_product_ek_product1_idx` (`product_id` ASC),
  INDEX `fk_ek_user_has_ek_product_ek_user1_idx` (`user_id` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_ek_user_has_ek_product_ek_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `kamille`.`ek_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_user_has_ek_product_ek_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `kamille`.`ek_product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_comment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_comment` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `date` DATETIME NOT NULL,
  `rating` TINYINT NOT NULL,
  `useful_counter` INT NOT NULL,
  `title` VARCHAR(128) NOT NULL,
  `comment` TEXT NOT NULL,
  `active` TINYINT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ek_product_comment_ek_product1_idx` (`product_id` ASC),
  INDEX `fk_ek_product_comment_ek_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_ek_product_comment_ek_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `kamille`.`ek_product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_comment_ek_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `kamille`.`ek_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_feature`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_feature` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_feature_value`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_feature_value` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `feature_id` INT NOT NULL,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ek_feature_value_ek_feature1_idx` (`feature_id` ASC),
  CONSTRAINT `fk_ek_feature_value_ek_feature1`
    FOREIGN KEY (`feature_id`)
    REFERENCES `kamille`.`ek_feature` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_has_feature`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_has_feature` (
  `product_id` INT NOT NULL,
  `feature_id` INT NOT NULL,
  `feature_value_id` INT NOT NULL,
  `position` TINYINT(1) NOT NULL,
  `technical_description` TEXT NOT NULL,
  PRIMARY KEY (`product_id`, `feature_id`),
  INDEX `fk_ek_product_has_ek_feature_ek_feature1_idx` (`feature_id` ASC),
  INDEX `fk_ek_product_has_ek_feature_ek_product1_idx` (`product_id` ASC),
  INDEX `fk_ek_product_has_feature_ek_feature_value1_idx` (`feature_value_id` ASC),
  CONSTRAINT `fk_ek_product_has_ek_feature_ek_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `kamille`.`ek_product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_has_ek_feature_ek_feature1`
    FOREIGN KEY (`feature_id`)
    REFERENCES `kamille`.`ek_feature` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_has_feature_ek_feature_value1`
    FOREIGN KEY (`feature_value_id`)
    REFERENCES `kamille`.`ek_feature_value` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_bundle`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_bundle` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  `active` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_bundle_has_product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_bundle_has_product` (
  `product_bundle_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  PRIMARY KEY (`product_bundle_id`, `product_id`),
  INDEX `fk_ek_product_bundle_has_ek_product_ek_product1_idx` (`product_id` ASC),
  INDEX `fk_ek_product_bundle_has_ek_product_ek_product_bundle1_idx` (`product_bundle_id` ASC),
  CONSTRAINT `fk_ek_product_bundle_has_ek_product_ek_product_bundle1`
    FOREIGN KEY (`product_bundle_id`)
    REFERENCES `kamille`.`ek_product_bundle` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_bundle_has_ek_product_ek_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `kamille`.`ek_product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_password_recovery_request`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_password_recovery_request` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `date_created` DATETIME NOT NULL,
  `code` VARCHAR(64) NOT NULL,
  `date_used` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ek_password_recovery_request_ek_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_ek_password_recovery_request_ek_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `kamille`.`ek_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_group` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_group_has_product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_group_has_product` (
  `product_group_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `order` INT NOT NULL,
  PRIMARY KEY (`product_group_id`, `product_id`),
  INDEX `fk_ek_product_group_has_ek_product_ek_product1_idx` (`product_id` ASC),
  INDEX `fk_ek_product_group_has_ek_product_ek_product_group1_idx` (`product_group_id` ASC),
  CONSTRAINT `fk_ek_product_group_has_ek_product_ek_product_group1`
    FOREIGN KEY (`product_group_id`)
    REFERENCES `kamille`.`ek_product_group` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_group_has_ek_product_ek_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `kamille`.`ek_product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_invoice`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_invoice` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NULL,
  `order_id` INT NOT NULL,
  `seller_id` INT NULL,
  `label` VARCHAR(256) NOT NULL,
  `invoice_number` VARCHAR(64) NOT NULL,
  `invoice_number_alt` VARCHAR(64) NULL,
  `invoice_date` DATETIME NOT NULL,
  `payment_method` VARCHAR(64) NOT NULL,
  `track_identifier` VARCHAR(512) NOT NULL,
  `amount` DECIMAL(20,2) NOT NULL,
  `seller` VARCHAR(64) NOT NULL,
  `user_info` BLOB NOT NULL,
  `seller_address` BLOB NOT NULL,
  `shipping_address` BLOB NOT NULL,
  `billing_address` BLOB NOT NULL,
  `invoice_details` MEDIUMBLOB NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `invoice_number_UNIQUE` (`invoice_number` ASC),
  UNIQUE INDEX `invoice_number_alt_UNIQUE` (`invoice_number_alt` ASC),
  INDEX `fk_ek_invoice_ek_order1_idx` (`order_id` ASC),
  INDEX `fk_ek_invoice_ek_user1_idx` (`user_id` ASC),
  INDEX `fk_ek_invoice_ek_seller1_idx` (`seller_id` ASC),
  CONSTRAINT `fk_ek_invoice_ek_order1`
    FOREIGN KEY (`order_id`)
    REFERENCES `kamille`.`ek_order` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_invoice_ek_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `kamille`.`ek_user` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_invoice_ek_seller1`
    FOREIGN KEY (`seller_id`)
    REFERENCES `kamille`.`ek_seller` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_seller_has_address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_seller_has_address` (
  `seller_id` INT NOT NULL,
  `address_id` INT NOT NULL,
  `order` INT NOT NULL,
  PRIMARY KEY (`seller_id`, `address_id`),
  INDEX `fk_ek_seller_has_ek_address_ek_address1_idx` (`address_id` ASC),
  INDEX `fk_ek_seller_has_ek_address_ek_seller1_idx` (`seller_id` ASC),
  CONSTRAINT `fk_ek_seller_has_ek_address_ek_seller1`
    FOREIGN KEY (`seller_id`)
    REFERENCES `kamille`.`ek_seller` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_seller_has_ek_address_ek_address1`
    FOREIGN KEY (`address_id`)
    REFERENCES `kamille`.`ek_address` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_direct_debit`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_direct_debit` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `invoice_id` INT NOT NULL,
  `date` DATETIME NOT NULL,
  `paid` TINYINT(1) NOT NULL,
  `feedback_details` TEXT NOT NULL,
  `amount` DECIMAL(20,6) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ek_payment_ek_invoice1_idx` (`invoice_id` ASC),
  CONSTRAINT `fk_ek_payment_ek_invoice1`
    FOREIGN KEY (`invoice_id`)
    REFERENCES `kamille`.`ek_invoice` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_newsletter`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_newsletter` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(128) NOT NULL,
  `subscribe_date` DATETIME NOT NULL,
  `unsubscribe_date` DATETIME NULL,
  `active` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_purchase_stat`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_purchase_stat` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `purchase_date` DATETIME NOT NULL,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_ref` VARCHAR(64) NOT NULL,
  `product_label` VARCHAR(64) NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(20,2) NOT NULL,
  `price_without_tax` DECIMAL(20,2) NOT NULL,
  `total` DECIMAL(20,2) NOT NULL,
  `total_without_tax` DECIMAL(20,2) NOT NULL,
  `attribute_selection` BLOB NOT NULL,
  `product_details_selection` BLOB NOT NULL,
  `wholesale_price` DECIMAL(20,2) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_provider`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_provider` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_tag`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_tag` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_cart`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_cart` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `date` DATETIME NOT NULL,
  `items` MEDIUMBLOB NOT NULL,
  `order_date` DATETIME NULL,
  `ip` VARCHAR(64) NOT NULL,
  `php_sess_id` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_card_has_product_attribute`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_card_has_product_attribute` (
  `product_card_id` INT NOT NULL,
  `product_attribute_id` INT NOT NULL,
  `order` TINYINT NOT NULL,
  PRIMARY KEY (`product_card_id`, `product_attribute_id`),
  INDEX `fk_ek_product_card_has_ek_product_attribute_ek_product_attr_idx` (`product_attribute_id` ASC),
  INDEX `fk_ek_product_card_has_ek_product_attribute_ek_product_card_idx` (`product_card_id` ASC),
  CONSTRAINT `fk_ek_product_card_has_ek_product_attribute_ek_product_card1`
    FOREIGN KEY (`product_card_id`)
    REFERENCES `kamille`.`ek_product_card` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_card_has_ek_product_attribute_ek_product_attrib1`
    FOREIGN KEY (`product_attribute_id`)
    REFERENCES `kamille`.`ek_product_attribute` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_store`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_store` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `address_id` INT NOT NULL,
  `label` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ek_physical_shop_ek_address1_idx` (`address_id` ASC),
  CONSTRAINT `fk_ek_physical_shop_ek_address1`
    FOREIGN KEY (`address_id`)
    REFERENCES `kamille`.`ek_address` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_has_tag`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_has_tag` (
  `product_id` INT NOT NULL,
  `tag_id` INT NOT NULL,
  PRIMARY KEY (`product_id`, `tag_id`),
  INDEX `fk_ek_product_has_ek_tag_ek_tag1_idx` (`tag_id` ASC),
  INDEX `fk_ek_product_has_ek_tag_ek_product1_idx` (`product_id` ASC),
  CONSTRAINT `fk_ek_product_has_ek_tag_ek_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `kamille`.`ek_product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_has_ek_tag_ek_tag1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `kamille`.`ek_tag` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_has_provider`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_has_provider` (
  `product_id` INT NOT NULL,
  `provider_id` INT NOT NULL,
  `wholesale_price` DECIMAL(20,2) NOT NULL,
  PRIMARY KEY (`product_id`, `provider_id`),
  INDEX `fk_ek_product_has_ek_provider_ek_provider1_idx` (`provider_id` ASC),
  INDEX `fk_ek_product_has_ek_provider_ek_product1_idx` (`product_id` ASC),
  CONSTRAINT `fk_ek_product_has_ek_provider_ek_product1`
    FOREIGN KEY (`product_id`)
    REFERENCES `kamille`.`ek_product` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ek_product_has_ek_provider_ek_provider1`
    FOREIGN KEY (`provider_id`)
    REFERENCES `kamille`.`ek_provider` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kamille`.`ek_product_card_image`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kamille`.`ek_product_card_image` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product_card_id` INT NOT NULL,
  `url` VARCHAR(64) NOT NULL,
  `legend` VARCHAR(128) NOT NULL,
  `position` INT NOT NULL,
  `active` TINYINT(1) NOT NULL,
  `is_default` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ek_product_card_image_ek_product_card1_idx` (`product_card_id` ASC),
  CONSTRAINT `fk_ek_product_card_image_ek_product_card1`
    FOREIGN KEY (`product_card_id`)
    REFERENCES `kamille`.`ek_product_card` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
