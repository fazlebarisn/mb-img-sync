
// Create urlParams query string
var urlParams = new URLSearchParams(window.location.search);

// Get value of single parameter
var sectionName = urlParams.get('j3-upload-img');

if(sectionName){

    sectionName = parseInt(sectionName) + 1;
    // sectionName = parseInt(sectionName);
    console.log(sectionName);
    var url = 'users.php?page=mbai-sync&j3-upload-img=' + sectionName;
    setTimeout(() => {
        window.location.replace(url);
    }, 2000);

}

// Create urlParams query string
var urlParams = new URLSearchParams(window.location.search);

// Get value of single parameter
var sectionName = urlParams.get('j3-sync-img');

if(sectionName){

    sectionName = parseInt(sectionName) + 1;
    // sectionName = parseInt(sectionName);
    console.log(sectionName);
    var url = 'users.php?page=mbai-sync&j3-sync-img=' + sectionName;
    setTimeout(() => {
        window.location.replace(url);
    }, 2000);

}

