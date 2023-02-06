const express = require("express");
const cors = require("cors");

const app = express();

var corsOptions = {
    origin: "http://localhost:8081"
};

app.use(cors(corsOptions));

// Parse requests of content-type - application/json
app.use(express.json());

// Parse requests of content-type - application/x-www-form-urlencoded
app.use(express.urlencoded({ extended: true}));

// Simple route
app.get("/", (req, res) => {
    res.json({ message: "Hello World"});
});

// require("./app/routes/tutorial.routes.js")(app);

// Set port. listen for requests
const PORT = process.env.PORT || 8080;

require("./app/routes/account.routes.js")(app);
require("./app/routes/profile.routes.js")(app);
require("./app/routes/history.routes.js")(app);
require("./app/routes/restaurant.routes.js")(app);
require("./app/routes/menu.routes.js")(app);
require("./app/routes/dish.routes.js")(app);
require("./app/routes/order.routes.js")(app);
require("./app/routes/allergy.routes.js")(app);
require("./app/routes/review.routes.js")(app);

app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}.`);
});
