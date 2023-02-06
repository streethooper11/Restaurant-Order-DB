
const sql = require("./db.js");

// Constructor
const Dish = function(dish) {
    this.Dish_ID = dish.Dish_ID;
    this.Dish_Name = dish.Dish_Name;
    this.Price = dish.Price;
    this.Category = dish.Category;
    this.RestaurantID = dish.RestaurantID;
};

// Getting dish by restaurant id 
Dish.findByRestaurantID = (RestaurantID, result) => {
    sql.query(`SELECT Dish_Name FROM dish WHERE RestaurantID = ${RestaurantID}`, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        if (res.length) {
            console.log("Found dish: ", res);
            result(null, res);
            return;
        }
        // Not found dish with restaurant name and menu name
        result({ kind: "not_found"}, null);
    });
};

// Getting dish by restaurant id 
Dish.specialGetDish_ID = (Dish_ID, result) => {
    sql.query(`SELECT Amount_Needed FROM Needs JOIN Dish WHERE Needs.Dish_ID = '${Dish_ID}'`, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        if (res.length) {
            console.log("Found dish: ", res);
            result(null, res);
            return;
        }
        // Not found dish with restaurant name and menu name
        result({ kind: "not_found"}, null);
    });
};



module.exports = Dish;


