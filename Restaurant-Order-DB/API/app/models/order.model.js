
const sql = require("./db.js");

// Constructor
const Order = function(order) {
    this.Order_ID = order.Order_ID;
    this.Order_Time = order.Order_Time;
    this.Total_Price = order.Total_Price;
    this.Email_ID = order.Email_ID;
    this.Profile_Name = order.Profile_Name;
    this.RestaurantID = order.RestaurantID;
};

// Creating new order 
Order.create = (newOrder, result) => {
    sql.query("INSERT INTO 471db.Order SET ?", newOrder, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        console.log("Created order: ", { id: res.insertID, ...newOrder });
        result(null, { id: res.insertID, ...newOrder} );
    });
};



// Getting order based on order ID
Order.findByID = (ID, result) => {
    sql.query(`SELECT * from 471db.Order WHERE Order_ID = ${ID}`, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        if (res.length) {
            console.log("Found order: ", res);
            result(null, res);
            return;
        }
        // Not found Order with Name
        result({ kind: "not_found"}, null);
    });
};

module.exports = Order;
