
const sql = require("./db.js");

// Constructor 
const Review = function(review) {
    this.Rating = review.Rating;
    this.Comment = review.Comment;
    this.UserEmail_ID = review.UserEmail_ID;
    this.Date_Time = review.Date_Time;
    this.Reply = review.Reply;
    this.AdminEmail_ID = review.AdminEmail_ID;
    this.Order_ID = review.Order_ID;
};

// Creating new review 
Review.create = (newReview, result) => {
    sql.query("INSERT INTO Review SET ?", newReview, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        console.log("Created account: ", { id: res.insertID, ...newReview });
        result(null, { id: res.insertID, ...newReview} );
    });
};



// Getting all reviews
//Review.getAll = (id, result) => {
//    let query = "SELECT * FROM review";
//    sql.query(query, (err, res) => {
//    if (err) {
//        console.log("error: ", err);
//        result(err, null);
//        return;
//    }
//        console.log("Review: ", res);
//        result(null, res);
//    });
//};

// Getting all review based on admin email ID 
Review.findByID = (AdminEmail_ID, result) => {
    sql.query(`SELECT * from review WHERE AdminEmail_ID = '${AdminEmail_ID}'`, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        } 
        if (res.length) {
            console.log("Found review: ", res);
            result(null, res);
            return;
        }
        // Not found Review with Name
        result({ kind: "not_found"}, null);
    });
};

module.exports = Review;
