document.addEventListener('readystatechange', (event) => {
    document.getElementById('RequestGrade').addEventListener('click', getGrade)
});

var fieldState = 0;

function FieldHandle(e) {
    if (e.keyCode === 13) {
        e.preventDefault();
        switch (fieldState) {
            case 0:
                document.getElementById('txtPassword').focus()
                break
            case 1:
                document.getElementById('txtSemesterCode').focus()
                break
            case 2:
                getGrade(e)
                break
        }
        fieldState += 1
    }
}

function getGrade(e) {
    // e.preventDefault()
    document.getElementById('api_error').setAttribute('style', 'display: none;')
    document.getElementById('RequestGrade').innerText = 'Loading'
    document.getElementById('RequestGrade').setAttribute('disabled', 'disabled')
    var username = document.getElementById('txtUsername').value
    var password = document.getElementById('txtPassword').value
    var urlencoded = new URLSearchParams();
    urlencoded.append("username", username);
    urlencoded.append("password", password);

    var requestOptions = {
        method: 'POST',
        body: urlencoded
    };

    fetch("index.php", requestOptions)
        .then(response => response.json())
        .then(result => processData(result))
        .catch(error => showError(error));

}

function processData(result) {
    document.getElementById('RequestGrade').innerText = 'Submit'
    document.getElementById('RequestGrade').removeAttribute('disabled')
    console.log(result)
    if (result.status) {
        document.getElementById('semesterTxt').innerText = semcodeToTxt(result.semester)
        document.getElementsByClassName('form-signin')[0].setAttribute('style', 'display: none;')
        document.getElementsByTagName('section')[0].removeAttribute('style')
        result.data.forEach(writeSubject)
    } else {
        showError(result.error_msg)
    }
}

function semcodeToTxt(semcode) {
    var year = Math.floor(semcode / 10)
    year += 2500 - 543
    var sem = semcode % 10
    var semtxt = ''
    switch (sem) {
        case 0:
            semtxt += 'Summer Session '
            break
        case 1:
            semtxt += 'Fisrt Semester '
            break
        case 2:
            semtxt += 'Second Semester '
            break
        default:
            semtxt += sem + ' Semester '
    }
    semtxt += year
    return semtxt
}

function showError(error) {
    document.getElementById('api_error').innerHTML = error
    document.getElementById('api_error').removeAttribute('style')
}

function writeSubject(sub) {
    console.log(sub)
    document.getElementById('result').appendChild(generateNewSub(sub))
}

function generateNewSub(data) {
    var subID = document.createElement('h5')
    subID.className = 'card-title'
    subID.innerText = data.code + ' Sec: ' + data.section

    var subName = document.createElement('h6')
    subName.className = 'card-subtitle mb-2 text-muted'
    subName.setAttribute('style', 'text-overflow:ellipsis;overflow: hidden;white-space: nowrap;')
    subName.innerText = data.name

    var cardContent = document.createElement('p')
    cardContent.className = 'card-subtitle mb-2 text-muted'
    if (data.grade !== null) {
        cardContent.innerHTML = 'Grade: ' + data.grade + '<br />Status: '
        if (data.status == 0) {
            cardContent.innerHTML += '<span class="badge badge-secondary">Unofficial</span>'
        } else {
            cardContent.innerHTML += '<span class="badge badge-primary">Official</span>'
        }
    } else {
        cardContent.innerHTML = '<br /><span class="badge badge-danger">No data</span>'
    }

    var cardbody = document.createElement('div')
    cardbody.className = 'card-body'
    cardbody.appendChild(subID)
    cardbody.appendChild(subName)
    cardbody.appendChild(cardContent)
    var card = document.createElement('div')
    card.className = 'card'
    card.appendChild(cardbody)
    var newsub = document.createElement('div')
    newsub.className = 'col-lg-3 align-items-stretch'
    newsub.appendChild(card)
    return newsub
}