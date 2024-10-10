PRAGMA foreign_keys=on;

.headers on
.mode columns
.nullvalue NULL

DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Categories;
DROP TABLE IF EXISTS Sizes;
DROP TABLE IF EXISTS Conditions;
DROP TABLE IF EXISTS Items;
DROP TABLE IF EXISTS Images;
DROP TABLE IF EXISTS ItemImages;
DROP TABLE IF EXISTS UserImage;
DROP TABLE IF EXISTS Chats;
DROP TABLE IF EXISTS Orders;
DROP TABLE IF EXISTS OrderItems;
DROP TABLE IF EXISTS Wishlists;
DROP TABLE IF EXISTS CheckoutInfo;
DROP TABLE IF EXISTS Ratings;

DROP TRIGGER IF EXISTS UpdateItemStatusAfterOrder;
DROP TRIGGER IF EXISTS ReactivateItemOnOrderCancel;


CREATE TABLE Users (
    idUser INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    isAdmin BOOLEAN NOT NULL DEFAULT 0
);

CREATE TABLE Categories (
    idCategory INTEGER PRIMARY KEY,
    categoryName TEXT UNIQUE NOT NULL
);

CREATE TABLE Sizes (
    idSize INTEGER PRIMARY KEY,
    sizeName TEXT UNIQUE NOT NULL
);

CREATE TABLE Conditions (
    idCondition INTEGER PRIMARY KEY,
    conditionName TEXT UNIQUE NOT NULL
);

CREATE TABLE Items (
    idItem INTEGER PRIMARY KEY AUTOINCREMENT,
    idSeller INTEGER NOT NULL,
    name TEXT NOT NULL,
    introduction TEXT,
    description TEXT,
    idCategory INTEGER,
    brand TEXT,
    model TEXT,
    idSize INTEGER,
    idCondition INTEGER,
    price REAL NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    featured BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (idSeller) REFERENCES Users(idUser),
    FOREIGN KEY (idCategory) REFERENCES Categories(idCategory),
    FOREIGN KEY (idSize) REFERENCES Sizes(idSize),
    FOREIGN KEY (idCondition) REFERENCES Conditions(idCondition)
);

CREATE TABLE Images (
    idImage INTEGER PRIMARY KEY AUTOINCREMENT,
    imagePath TEXT NOT NULL
);

CREATE TABLE ItemImages (
    idItemImage INTEGER PRIMARY KEY AUTOINCREMENT,
    idItem INTEGER NOT NULL,
    idImage INTEGER NOT NULL,
    isMain BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (idItem) REFERENCES Items(idItem),
    FOREIGN KEY (idImage) REFERENCES Images(idImage)
);

CREATE TABLE UserImage (
    idUserImage INTEGER PRIMARY KEY AUTOINCREMENT,
    idUser INTEGER NOT NULL,
    idImage INTEGER NOT NULL,
    FOREIGN KEY (idUser) REFERENCES Users(idUser),
    FOREIGN KEY (idImage) REFERENCES Images(idImage)
);

CREATE TABLE Chats (
    idChat INTEGER PRIMARY KEY,
    idSender INTEGER NOT NULL,
    idRecipient INTEGER NOT NULL,
    message TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    idItem INTEGER NOT NULL,
    FOREIGN KEY (idSender) REFERENCES Users(idUser),
    FOREIGN KEY (idRecipient) REFERENCES Users(idUser),
    FOREIGN KEY (idItem) REFERENCES Items(idItem)
);

CREATE TABLE Orders (
    idOrder INTEGER PRIMARY KEY,
    idBuyer INTEGER NOT NULL,
    totalPrice REAL NOT NULL,
    orderDate TEXT DEFAULT CURRENT_TIMESTAMP,
    status TEXT NOT NULL DEFAULT 'Pending',
    CONSTRAINT CHECK_Status CHECK (status = 'Pending' OR status='Done' OR status='Canceled'),
    FOREIGN KEY (idBuyer) REFERENCES Users(idUser)
);

CREATE TABLE OrderItems (
    idOrderItem INTEGER PRIMARY KEY,
    idOrder INTEGER NOT NULL,
    idItem INTEGER NOT NULL,
    sent BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (idOrder) REFERENCES Orders(idOrder),
    FOREIGN KEY (idItem) REFERENCES Items(idItem)
);

CREATE TABLE Wishlists (
    idWishlist INTEGER PRIMARY KEY,
    idUser INTEGER NOT NULL,
    idItem INTEGER NOT NULL,
    FOREIGN KEY (idUser) REFERENCES Users(idUser),
    FOREIGN KEY (idItem) REFERENCES Items(idItem)
);

CREATE TABLE CheckoutInfo (
    idCheckout INTEGER PRIMARY KEY AUTOINCREMENT,
    idOrder INTEGER NOT NULL,
    address TEXT NOT NULL,
    city TEXT NOT NULL,
    zipCode TEXT NOT NULL,
    paymentMethod TEXT NOT NULL,
    FOREIGN KEY (idOrder) REFERENCES Orders(idOrder)
);

CREATE TABLE Ratings (
    idRating INTEGER PRIMARY KEY AUTOINCREMENT,
    idUser INTEGER NOT NULL,
    rating INTEGER NOT NULL,
    comment TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT CHECK_Rating CHECK (rating BETWEEN 1 AND 5),
    FOREIGN KEY (idUser) REFERENCES Users(idUser)
);


CREATE TRIGGER UpdateItemStatusAfterOrder
AFTER INSERT ON OrderItems
BEGIN
    UPDATE Items
    SET active = FALSE
    WHERE idItem = NEW.idItem;
END;

CREATE TRIGGER ReactivateItemOnOrderCancel
AFTER UPDATE OF status ON Orders
WHEN NEW.status = 'Canceled'
BEGIN
    UPDATE Items
    SET active = TRUE
    WHERE idItem = (SELECT idItem FROM OrderItems WHERE idOrder = NEW.idOrder);
END;

CREATE TRIGGER UpdateOrderStatusToDone
AFTER UPDATE OF sent ON OrderItems
FOR EACH ROW
WHEN NEW.sent = 1
BEGIN
    UPDATE Orders 
    SET status = 'Done' 
    WHERE idOrder = NEW.idOrder
    AND NOT EXISTS (
        SELECT idItem 
        FROM OrderItems 
        WHERE idOrder = NEW.idOrder 
        AND sent = FALSE
    );
END;




INSERT INTO Users (name, username, password, email, isAdmin) VALUES
('Leonardo Teixeira', 'leo', '$2y$10$4zN2fHMbSNK1tI82oS8JBeoFRJ6PNUPe6E6ZpUswZr5remJUk/0hu', 'leo@gmail.com', 1),
('Cristiano Ronaldo', 'paicris', '$2y$10$4zN2fHMbSNK1tI82oS8JBeoFRJ6PNUPe6E6ZpUswZr5remJUk/0hu', 'cr7@gmail.com', 1),
('Neymar Jr', 'neymito', '$2y$10$4zN2fHMbSNK1tI82oS8JBeoFRJ6PNUPe6E6ZpUswZr5remJUk/0hu', 'ney@gmail.com', 0),
('Diogo Silva Vieira', 'dioguerox', '$2y$10$4zN2fHMbSNK1tI82oS8JBeoFRJ6PNUPe6E6ZpUswZr5remJUk/0hu', 'vieiradiogo525@gmail.com', 1),
('David Carvalho', 'davidcarvalho', '$2y$10$4zN2fHMbSNK1tI82oS8JBeoFRJ6PNUPe6E6ZpUswZr5remJUk/0hu', 'davidgustavocc@gmail.com', 1),
('João Silva', 'joaosilva', '$2y$10$4zN2fHMbSNK1tI82oS8JBeoFRJ6PNUPe6E6ZpUswZr5remJUk/0hu', 'joaosilva@gamil.com', 0),
('Ricardo Pereira', 'ricardopereira', '$2y$10$4zN2fHMbSNK1tI82oS8JBeoFRJ6PNUPe6E6ZpUswZr5remJUk/0hu', 'ricardo@gmail.com', 0);

INSERT INTO Categories (categoryName) VALUES
('Electronics'),
('Clothing'),
('Furniture'),
('Books'),
('Games'),
('Sports'),
('Homeware'),
('Others');

INSERT INTO Sizes (sizeName) VALUES
('Extra Small'),
('Small'),
('Medium'),
('Large'),
('Extra Large');

INSERT INTO Conditions (conditionName) VALUES
('New'),
('Like New'),
('Good'),
('Fair'),
('Used');

INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (1, 'Smartphone', 'gently used smartphone', 'A gently used smartphone in excellent condition. Comes with charger and original packaging.', 1, 'Samsung', 'Galaxy S10', 1, 2, 200, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (1, 'Bicycle', 'nice bicycle', 'A sturdy bicycle perfect for commuting or leisure rides. Includes a basket for carrying items.', 6, 'Schwinn', 'Cruiser', 3, 2, 150, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (1, 'Laptop', 'powerful laptop', 'A powerful laptop suitable for work and entertainment. Features a fast processor and ample storage.', 1, 'Dell', 'XPS 15', 2, 1, 800, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (1, 'Elegant Dress', 'elegant dress', 'An elegant dress perfect for formal occasions or evening events. Made from high-quality fabric with exquisite design details.', 2, 'Ralph Lauren', 'Elegant Evening Gown', 2, 1, 120, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (1, 'Stylish Shoes', 'nice shoes', 'A pair of stylish and comfortable shoes suitable for everyday wear. Features durable material and a sleek design.', 2, 'Nike', 'Air Max', 1, 1, 70, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (1, 'Soccer Ball', 'nice soccer ball', 'A high-quality soccer ball suitable for training or matches. Features durable material and a classic design.', 6, 'Adidas', 'Tango', 3, 3, 30, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (2, 'Smartwatch', 'latest smartwatch model', 'Stay connected with this stylish smartwatch. Features include fitness tracking, notifications, and more.', 1, 'Apple', 'Watch Series 6', 1, 1, 400, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (3, 'Gaming Console', 'next-gen gaming experience', 'Experience immersive gaming with this powerful gaming console. Supports 4K gaming and streaming services.', 5, 'Sony', 'PlayStation 5', 1, 1, 500, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (4, 'Coffee Table', 'modern coffee table', 'Add a touch of style to your living room with this sleek coffee table. Features a durable build and ample storage space.', 3, 'IKEA', 'Lack Coffee Table', 4, 3, 80, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (5, 'Leather Jacket', 'classic leather jacket', 'Make a fashion statement with this timeless leather jacket. Crafted from high-quality leather for durability and style.', 2, 'ZARA', 'Classic Leather Jacket', 3, 2, 180, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (6, 'Running Shoes', 'premium running shoes', 'Achieve your fitness goals with these premium running shoes. Designed for comfort and performance during your workouts.', 6, 'Under Armour', 'UA HOVR™ Infinite 2', 2, 1, 120, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (5, 'Tablet', 'sleek tablet design', 'Stay productive and entertained with this sleek tablet. Features a high-resolution display and long battery life.', 1, 'Samsung', 'Galaxy Tab S7', 2, 3, 300, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (4, 'Gaming Chair', 'ergonomic gaming chair', 'Enhance your gaming experience with this ergonomic gaming chair. Features adjustable settings for comfort during long gaming sessions.', 3, 'DXRacer', 'Formula Series', 4, 3, 250, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (2, 'Desk', 'modern desk design', 'Create a stylish workspace with this modern desk. Features a minimalist design and ample storage space for your essentials.', 3, 'IKEA', 'Micke Desk', 4, 2, 100, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (4, 'Portuguese Notebook', 'Portuguese notebook', 'A notebook for Portuguese classes. It has 200 pages and is in good condition.', 4, 'Porto Editora', 'Caderno de Português', 1, 3, 5, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (5, 'English Notebook', 'English notebook', 'A notebook for English classes. It has 200 pages and is in good condition.', 4, 'Oxford', 'English Notebook', 1, 3, 5, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (6, 'Math Notebook', 'Math notebook', 'A notebook for Math classes. It has 200 pages and is in good condition.', 4, 'Casio', 'Math Notebook', 1, 3, 5, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (3, 'PS3', 'Playstation 3', 'A used PS3 in good condition. Comes with 2 controllers and 5 games.', 5, 'Sony', 'Playstation 3', 5, 4, 100, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (2, 'PS4', 'Playstation 4', 'A used PS4 in good condition. Comes with 2 controllers and 5 games.', 5, 'Sony', 'Playstation 4', 5, 4, 200, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (4, 'Padel Racket', 'Padel Racket', 'A used padel racket in good condition. Comes with a cover.', 6, 'Bullpadel', 'Vertex 2', 4, 2, 140, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (5, 'Microwave', 'Microwave', 'A used microwave in good condition. Perfect for heating up meals quickly.', 7, 'LG', 'NeoChef', 1, 3, 130, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (6, 'Sofa', 'Sofa', 'A used sofa in good condition. Comfortable and stylish for your living room.', 7, 'IKEA', 'Kivik', 4, 2, 300, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (2, 'Toaster', 'Toaster', 'A used toaster in good condition. Perfect for making toast and bagels.', 7, 'Cuisinart', 'Classic Toaster', 1, 3, 45, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (3, 'Motorcicle', 'Motorcicle', 'A used motorcicle in good condition. Perfect for commuting or leisure rides.', 8, 'Yamaha', 'FZ6', 3, 2, 1500, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (4, 'Toolbox', 'Toolbox', 'A used toolbox in good condition. Perfect for storing and organizing your tools.', 8, 'Stanley', 'Toolbox', 4, 2, 50, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (1, 'Necklace', 'Necklace', 'A used necklace in good condition. Perfect for adding a touch of elegance to your outfit.', 8, 'Pandora', 'Necklace', 1, 3, 200, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (3, 'Football Goal', 'Football Goal', 'A football goal in good condition.', 6, 'Exit Scala', 'Football Goal', 3, 3, 70, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (4, 'Xbox One', 'Xbox One', 'A console in excelent condition. Perfect for an incredible time', 5, 'Xbox', 'Xbox One', 3, 3, 20, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (5, 'Fridge', 'Fridge', 'A fridge in excelent condition. Perfect for cooling all your food.', 7, 'LG', 'Door-in-Door', 5, 2, 150, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (6, 'T-shirt', 'T-shirt', 'A T-shirt in marvelous condition.', 2, 'Nike', 'Nike Black', 2, 2, 30, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (2, 'AirPods', 'AirPods', 'AirPods in good condition.', 1, 'Apple', 'AirPods Pro', 1, 2, 120, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (3, 'Sonia Chair Blue', 'Sonia Chair Blue', 'Blue Chair in excellent condition. ', 3, 'Target', 'Sonia Blue', 3, 3, 80, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (5, 'Os Maias', 'Os Maias', 'Book Os Maias in excellent condition.', 4, 'Porto Editora', 'Os Maias', 2, 2, 15, 1);
INSERT INTO Items (idSeller, name, introduction, description, idCategory, brand, model, idSize, idCondition, price, featured)
VALUES (2, 'Black Fusion Bracelet', 'Black Fusion Bracelet', 'Bracelet in excellent condition.', 8, 'Blue Bird', 'Black Fusion', 1, 2, 60, 1);


INSERT INTO Images (imagePath) VALUES
('../docs/itemImages/smartphone.jpg'),
('../docs/itemImages/smartphone2.jpg'),
('../docs/itemImages/smartphone3.jpg'),
('../docs/itemImages/bicycle.jpg'),
('../docs/itemImages/laptop.jpg'),
('../docs/itemImages/dress.jpg'),
('../docs/itemImages/shoes.jpg'),
('../docs/userImages/ronaldo.jpg'),
('../docs/itemImages/ball_adidas.jpg'),
('../docs/itemImages/adidas_ball_2.jpg'),
('../docs/itemImages/apple_watch_1.jpg'),
('../docs/itemImages/apple_watch_2.jpg'),
('../docs/itemImages/coffee_table.jpg'),
('../docs/itemImages/coffee_table_2.jpg'),
('../docs/itemImages/leather_jacket_1.jpg'),
('../docs/itemImages/leather_jacket_2.jpg'),
('../docs/itemImages/ps5_1.jpg'),
('../docs/itemImages/ps5_2.jpg'),
('../docs/itemImages/runn_1.jpg'),
('../docs/itemImages/runn_2.jpg'),
('../docs/itemImages/tab7_1.jpg'),
('../docs/itemImages/tab7_2.jpg'),
('../docs/itemImages/gaming_chair_1.jpg'),
('../docs/itemImages/gaming_chair_2.jpg'),
('../docs/itemImages/desk_1.jpg'),
('../docs/itemImages/desk_2.jpg'),
('../docs/itemImages/livro_pt_1.jpg'),
('../docs/itemImages/livro_pt_2.jpg'),
('../docs/itemImages/livro_mat_1.jpg'),
('../docs/itemImages/livro_mat_2.jpg'),
('../docs/itemImages/livro_ing_1.jpg'),
('../docs/itemImages/livro_ing_2.jpg'),
('../docs/itemImages/ps3_1.jpg'),
('../docs/itemImages/ps3_2.jpg'),
('../docs/itemImages/ps4_1.jpg'),
('../docs/itemImages/ps4_2.jpg'),
('../docs/itemImages/padel_1.jpg'),
('../docs/itemImages/padel_2.jpg'),
('../docs/itemImages/microwave_1.jpg'),
('../docs/itemImages/microwave_2.jpg'),
('../docs/itemImages/sofa_1.jpg'),
('../docs/itemImages/sofa_2.jpg'),
('../docs/itemImages/toaster_1.jpg'),
('../docs/itemImages/toaster_2.jpg'),
('../docs/itemImages/motorcicle_1.jpg'),
('../docs/itemImages/motorcicle_2.jpg'),
('../docs/itemImages/tool_kit_1.jpg'),
('../docs/itemImages/tool_kit_2.jpg'),
('../docs/itemImages/necklace_1.jpg'),
('../docs/itemImages/necklace_2.jpg'),
('../docs/itemImages/football_goal.jpg'),
('../docs/itemImages/xbox.jpg'),
('../docs/itemImages/fridge.jpg'),
('../docs/itemImages/t-shirt.jpg'),
('../docs/itemImages/airPods.jpg'),
('../docs/itemImages/chair.jpg'),
('../docs/itemImages/maias.jpg'),
('../docs/itemImages/bracelet.jpg');

INSERT INTO ItemImages (idItem, idImage, isMain) VALUES
(1, 1, TRUE),
(1, 2, FALSE),
(1, 3, FALSE),
(2, 4, TRUE),
(3, 5, TRUE),
(4, 6, TRUE),
(5, 7, TRUE),
(6, 9, TRUE),
(6, 10, FALSE),
(7, 11, TRUE),
(7, 12, FALSE),
(9, 13, TRUE),
(9, 14, FALSE),
(8, 17, TRUE),
(8, 18, FALSE),
(10, 15, TRUE),
(10, 16, FALSE),
(11, 19, TRUE),
(11, 20, FALSE),
(12, 21, FALSE),
(12, 22, TRUE),
(13, 23, TRUE),
(13, 24, FALSE),
(14, 25, TRUE),
(14, 26, FALSE),
(15, 27, FALSE),
(15, 28, TRUE),
(16, 31, TRUE),
(16, 32, FALSE),
(17, 30, TRUE),
(17, 29, FALSE),
(18, 33, FALSE),
(18, 34, TRUE),
(19, 35, TRUE),
(19, 36, FALSE),
(20, 37, TRUE),
(20, 38, FALSE),
(21, 39, TRUE),
(21, 40, FALSE),
(22, 41, TRUE),
(22, 42, FALSE),
(23, 43, FALSE),
(23, 44, TRUE),
(24, 45, TRUE),
(24, 46, FALSE),
(25, 47, TRUE),
(25, 48, FALSE),
(26, 49, TRUE),
(26, 50, FALSE),
(27, 51,TRUE),
(28, 52,TRUE),
(29,53,TRUE),
(30,54,TRUE),
(31,55,TRUE),
(32,56,TRUE),
(33,57,TRUE),
(34,58,TRUE);

INSERT INTO UserImage (idUser, idImage) VALUES
(2, 8);

INSERT INTO Chats (idSender, idRecipient, message, idItem) VALUES
(2, 1, 'Hello im interested!' , 1),
(1, 2, 'Hello come to al-nassr!', 1),
(2, 1, 'I will come tomorrow', 1),
(1, 2, 'Ok see you then!', 1),
(3, 1, 'Estou interessado', 1),
(1, 3, 'Olá, como posso ajudar?', 1),
(3, 1, 'Estou interessado, me ajuda!', 2),
(1, 3, 'É de graça pra vc', 2);

INSERT INTO Orders (idBuyer, totalPrice) VALUES
(2, 200.00),
(2, 150.00);

INSERT INTO OrderItems (idOrder, idItem) VALUES
(1, 1),
(2, 2);

INSERT INTO Wishlists (idUser, idItem) VALUES
(3, 3),
(3, 4),
(2, 4),
(2, 1),
(3, 5);

INSERT INTO Ratings (idUser, rating, comment) VALUES
(1, 5, 'Great seller!'),
(2, 4, 'Good experience, would buy again!'),
(3, 3, 'Item was as described.');