var price = document.getElementById('price');
price.addEventListener('blur', function () {
    if(this.value === '') {
        return;
    }
    this.setAttribute('type', 'text');
    if (this.value.indexOf('.') === -1) {
        this.value = this.value + '.00';
    }
    while (this.value.indexOf('.') > this.value.length - 3) {
        this.value = this.value + '0';
    }
});
price.addEventListener('focus', function () {
    this.setAttribute('type', 'number');
});

function previewFile(){
    //Get preview space
    var preview = document.querySelector('img');
    //Get image file
    var file = document.querySelector('input[type=file]').files[0];
    //Create a file reader
    var reader = new FileReader();

    reader.addEventListener("load", function () {
        preview.src = reader.result;
    }, false);

    if (file) {
        reader.readAsDataURL(file);
    }
}
