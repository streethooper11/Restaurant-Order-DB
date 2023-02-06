
const sql = require("./db.js");

// Constructor 
const Menu = function(menu) {
    this.RestaurantID = menu.RestaurantID;
    this.Menu_Name = menu.Menu_Name;
    this.Dish_ID = menu.Dish_ID;
};

// Getting all menus
Menu.getAll = (id, result) => {
    let query = "SELECT * FROM menu";
    sql.query(query, (err, res) => {
    if (err) {
        console.log("error: ", err);
        result(err, null);
        return;
    }
        console.log("Menu: ", res);
        result(null, res);
    });
};

// Getting menu based on restaurant ID 
Menu.findByID = (ID, result) => {
    sql.query(`SELECT * from menu WHERE RestaurantID = ${ID}`, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        } 
        if (res.length) {
            console.log("Found menu: ", res);
            result(null, res);
            return;
        }
        // Not found Menu with Name
        result({ kind: "not_found"}, null);
    });
};

module.exports = Menu;
