
async function searchMovie() {
    var input_field = document.querySelector('input').value;
    if (input_field != "") {
        var submitButton = document.querySelector('button'); 
        submitButton.innerText = 'Submitting';
        var title = document.querySelector('#Title');
        var date = document.querySelector('#releaseYear');
        var poster = document.querySelector('#poster');
        var details = document.querySelector('#details');
	localStorage.setItem("Movie name",input_field)
        try {
            const response = await fetch(`<path-to-connection.php>?q=${input_field}`);
            const data = await response.json();

            console.log('the response is', data);

            if (data && data.length > 0) { 
                submitButton.innerText = 'Submit'; 
                date.innerText = data[0].release_year;
                poster.src = data[0].poster;
                details.innerText = data[0].details;
            } else {
                submitButton.value = 'Submit';
                title.innerText = "Not found";
                date.innerText = "";
                poster.src = "";
                details.innerText = `${input_field} is invalid`;
            }

        } catch (error) {
            submitButton.innerText = 'Submit';
            console.error(error);
            title.innerText = "Error fetching";
            date.innerText = "";
            poster.src = "";
            details.innerText = "Error fetching";
        }
    } else {
        alert("Please dont enter empty string");
    }
}
