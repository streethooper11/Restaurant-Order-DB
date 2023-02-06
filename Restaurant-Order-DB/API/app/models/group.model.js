
const sql = require("./db.js");

// Constructor
const Group = function(group) {
    this.Group_ID = group.Group_ID;
    this.Email_ID = group.Email_ID;
};

// Creating new group
Group.create = (newGroup, result) => {
    sql.query("INSERT INTO OrderGroup SET ?", newGroup, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        console.log("Created new group: ", {...newGroup });
        result(null, { id: res.insertID, ...newGroup});
    });
};

// Updating group, adding emailID into group
Group.updateByGroup_ID = (Group_ID, group, result) => {
    sql.query("INSERT INTO OrderGroup SET Email_ID = ?, Group_ID = ?", [group.Email_ID, Group_ID],
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
            console.log("Updated group: ", {...group});
            result(null, {...group});
        }
    );
};

// Get by GroupID
Group.findByGroupID = (Group_ID, result) => {
    sql.query(`SELECT * FROM OrderGroup WHERE Group_ID = ${Group_ID}`, (err, res) => {
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

module.exports = Group;
