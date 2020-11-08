CREATE DATABASE Shipping;

CREATE TABLE Client(
ClientID int AUTO_INCREMENT,
BusinessName varchar(50) NOT NULL UNIQUE,
CONSTRAINT PRIMARY KEY (ClientID)
);

CREATE TABLE Carrier(
CarrierID INT AUTO_INCREMENT,
CarrierName varchar(20) NOT NULL UNIQUE,
TrackingSite varchar(60),
CONSTRAINT PRIMARY KEY (CarrierID)
);

CREATE TABLE Shipment(
ShipmentID INT AUTO_INCREMENT,
ClientID INT NOT NULL,
CarrierID INT NOT NULL,
ItemsShipping varchar (100) NOT NULL,
EstShipDate DATE,
EstDelivery DATE,
TrackingNum VARCHAR(30) NOT NULL,
Status VARCHAR(10) NOT NULL,
Notes VARCHAR(100),
DateEntered DATE,
CONSTRAINT PRIMARY KEY (ShipmentID),
CONSTRAINT FOREIGN KEY (ClientID) REFERENCES Client(ClientID) ON DELETE CASCADE,
CONSTRAINT FOREIGN KEY (CarrierID) REFERENCES Carrier(CarrierID) ON DELETE CASCADE
);

CREATE TABLE Users(
Username VARCHAR(20),
Password VARCHAR(100) NOT NULL,
AccessLevel INT NOT NULL,
CONSTRAINT PRIMARY KEY (Username)
);

/*Create stored procedures to increase performance of queries*/
DELIMITER //
CREATE PROCEDURE FindClient(IN clientName VARCHAR(30))
BEGIN
    SELECT ClientID FROM Client WHERE BusinessName=clientName;
END//

CREATE PROCEDURE FindCarrier(IN carrierin VARCHAR(10))
BEGIN
    SELECT CarrierID FROM Carrier WHERE CarrierName=carrierin;
END//

CREATE PROCEDURE viewShips(IN shipID INT)
BEGIN
    SELECT Shipment.ShipmentID, Client.ClientID, Client.BusinessName, Shipment.ItemsShipping, Shipment.EstDelivery, Shipment.Status, Carrier.CarrierID,
				Carrier.CarrierName, Shipment.TrackingNum, Shipment.Notes, Shipment.DateEntered FROM ((Shipment INNER JOIN Client ON Client.ClientID=Shipment.ClientID)
				JOIN Carrier ON Carrier.CarrierID=Shipment.CarrierID) WHERE ShipmentID=shipID;
END//

CREATE PROCEDURE insertShip(IN client INT, IN carrier INT, IN item VARCHAR(100), IN shipped DATE, IN deliver DATE, IN track INT, IN stat VARCHAR(10), IN notes VARCHAR(100), IN entered DATE)
BEGIN
    INSERT INTO Shipping.Shipment (ClientID, CarrierID, ItemsShipping, EstShipDate, EstDelivery, TrackingNum, Status, Notes, DateEntered)
				VALUES (client, carrier, item, shipped, deliver, track, stat, notes, entered);
END//

CREATE PROCEDURE newClient(IN client VARCHAR(50))
BEGIN
    INSERT IGNORE INTO Shipping.Client (BusinessName) VALUES (client);
END//

DELIMITER ;
