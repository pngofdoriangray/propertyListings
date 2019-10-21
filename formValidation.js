function validateForm() {
    var county = document.getElementById('county');
    var country = document.getElementById('country');
    var town = document.getElementById('town');
    var postcode = document.getElementById('postcode');
    var description = document.getElementById('description');
    var image = document.getElementById('inputImage');
    var numbed = document.getElementById('numbed');
    var numbath = document.getElementById('numbath');
    var price = document.getElementById('price');
    var propertyType = document.getElementById('propertyType');

    if (county.value === '') {
        alert("You must provide the County.");
        county.focus();
        return false;
    }
    if (country.value === "") {
        alert("You must provide the Country.");
        country.focus();
        return false;
    }
    if (town.value === "") {
        alert("You must provide the Town.");
        town.focus();
        return false;
    }
    if (postcode.value === "") {
        alert("You must provide the Postcode.");
        postcode.focus();
        return false;
    }
    if (description.value === "") {
        alert("You must provide a description.");
        description.focus();
        return false;
    }
    if (image.value == "" || image.value == null)
    {
        alert("You must provide an image.");
        image.focus();
        return false;
    }
    if (numbed.value == "" || numbed.value == null || isNaN(numbed.value)) {
        alert("You must provide the number of bedrooms.");
        numbed.focus();
        return false;
    }
    if (numbath.value == "" || numbath.value == null || isNaN(numbath.value)) {
        alert("You must provide the number of bathrooms.");
        numbath.focus();
        return false;
    }
    if (price.value == "" || price.value == null || isNaN(price.value)) {
        alert("You must provide the price.");
        price.focus();
        return false;
    }
    if (propertyType.value === "") {
        alert("You must provide the property type.");
        propertyType.focus();
        return false;
    }
}

