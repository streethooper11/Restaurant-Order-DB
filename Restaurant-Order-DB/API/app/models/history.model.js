
const sql = require("./db.js");

// Constructor
const History = function(history) {
    this.Order_ID = history.Order_ID;
    this.UserEmail_ID = history.UserEmail_ID;
    this.Total_Price = history.Total_Price;
    this.Order_Place = history.Order_Place;
    this.History_ID = history.History_ID;
};

// Creating new group
History.create = (newHistory, result) => {
    sql.query("INSERT INTO History SET ?", newHistory, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        console.log("Created new group: ", {...newHistory });
        result(null, { id: res.insertID, ...newHistory});
    });
};

// Updating group, adding emailID into group
History.updateByGroup_ID = (History_ID, history, result) => {
    sql.query("INSERT INTO History SET UserEmail_ID = ?, History_ID = ?", [history.UserEmail_ID, History_ID],
    //sql.query("UPDATE OrderGroup SET Email_ID = ? WHERE Group_ID = ?", [group.Email_ID, Group_ID],
        (err, res) => {
            if (err) {
                console.log("error: ", err);
                result(null, err);
                return;
            }
            if (res.affectedRows == 0) {
                // not found Group with the id
                result({ kind: "not_found" }, null);
                return;
            }
            console.log("Updated group: ", {...history});
            result(null, {...history});
        }
    );
};

// Get by GroupID
History.findByGroupID = (History_ID, result) => {
    sql.query(`SELECT * FROM History WHERE History_ID = ${History_ID}`, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        if (res.length) {
            console.log("Found group ", res);
            result(null, res);
            return;
        }
        // Not found Group with GroupID
        result({ kind: "not_found"}, null);
    });
};

module.exports = History;
