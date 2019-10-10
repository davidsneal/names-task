<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Names</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Hepta+Slab:400,700&display=swap" rel="stylesheet">
        <link href="https://use.fontawesome.com/releases/v5.0.7/css/all.css" rel="stylesheet" crossorigin="anonymous">

        <!-- Styles -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <!-- Custom styling -->
        <style>
            body {
                font-family: 'Hepta Slab';
            }

            .spinner {
                font-size: 35px;
            }

            .exclamation {
                font-size: 35px;
                margin-bottom: 35px;
            }
        </style>
    </head>
    <body>
        <div class="container pt-5">
            <div class="row">
                <div class="col text-center">
                    <h1>Names</h1>
                </div>
            </div>
            <div class="pt-4">
                <div id="loading" class="row">
                    <div class="col text-primary text-center">
                        <i class="spinner fas fa-circle-notch fa-spin"></i>
                        <p>Loading...</p>
                    </div>
                </div>
                <div id="error" class="row d-none">
                    <div class="col text-danger text-center">
                        <i class="exclamation fas fa-exclamation-triangle"></i>
                        <p>Something went wrong, click below to try again.</p>
                        <button id="retryButton" class="btn btn-danger">
                            <i class="fas fa-fw fa-redo mr-2"></i> Retry
                        </button>
                    </div>
                </div>
                <form id="searchForm" class="d-none">
                    <div class="form-row align-items-center justify-content-end pb-4">
                        <div class="col-auto">
                            <label class="sr-only" for="searchInput">Name</label>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search names...">
                        </div>
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="showDuplicatesCheck">
                                <label class="form-check-label" for="showDuplicatesCheck">
                                    Show duplicates
                                </label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button id="searchButton" type="submit" class="btn btn-primary">
                                <i class="fas fa-fw fa-search mr-2"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
                <div id="results" class="row d-none">
                    <div class="col">
                        <table id="resultsTable" class="table table-dark table-striped table-borderless table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>First name</th>
                                    <th>Last name</th>
                                </tr>
                            </thead>
                            <tbody id="resultsTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div id="pagination" class="row d-none">
                    <div class="col">

                    </div>
                </div>
                <div id="resultsCount" class="row d-none">
                    <div class="col text-center">
                        <p>
                            Displaying <strong id="displayedCount"></strong> of
                            <strong id="totalCount"></strong> results.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // get the url parameters (if present)
            const urlParams = new URLSearchParams(window.location.search);

            // the current page or default to 1
            const currentPage = urlParams.get('page') || 1

            // get the current search (falls back to null if not set)
            const searchTerm = urlParams.get('terms')

            // get all the HTML elements we shall need
            const displayedCount = document.getElementById('displayedCount')
                error = document.getElementById('error')
                loading = document.getElementById('loading')
                pagination = document.getElementById('pagination')
                results = document.getElementById('results')
                resultsCount = document.getElementById('resultsCount')
                resultsTable = document.getElementById('resultsTable')
                resultsTableBody = document.getElementById('resultsTableBody')
                retryButton = document.getElementById('retryButton')
                searchButton = document.getElementById('searchButton')
                searchForm = document.getElementById('searchForm')
                searchInput = document.getElementById('searchInput')
                totalCount = document.getElementById('totalCount')

            // add a listener to the retry button to reload the page onclick
            retryButton.addEventListener('click', e => location.reload())

            // add a listener to the retry button to reload the page onclick
            searchButton.addEventListener('click', (e) => {
                // prevent the default submit action
                e.preventDefault()

                // submit the form using the getData method
                getData()
            })

            // add a short timeout simply to show the loading div
            setTimeout(() => getData(), 500)

            // get the data and display it
            const getData = () => {
                // init XMLHttpRequest
                const xhr = new XMLHttpRequest()

                // make the GET request to the names API
                xhr.open('GET', prepareUrl())

                // the request transaction is complete
                xhr.onload = () => {
                    // if the request was successful
                    if (xhr.status === 200) {
                        // parse the json response
                        const response = JSON.parse(xhr.response)

                        // populate the table with the data
                        populateTable(response.data)

                        // build the pagination links as/if required
                        buildPagination(response.meta)

                        // hide the loading div
                        loading.classList.add('d-none')

                        // show the required divs
                        results.classList.remove('d-none')
                        resultsCount.classList.remove('d-none')
                        searchForm.classList.remove('d-none')
                    } else {
                        // hide the required divs
                        loading.classList.add('d-none')

                        // show the error div
                        error.classList.remove('d-none')
                    }
                }

                // send the request
                xhr.send();
            }

            // prepare the url to call, based on form input/pagination state
            const prepareUrl = () => {
                // define/set the base url, with the current page set
                let url = `api/names?page=${currentPage}`

                // if there's a search term
                if (searchInput.value.length) {
                    // add the search term to the url
                    url += `&term=${searchInput.value}`
                }

                // add the duplicates boolean
                url += `&dupes=${showDuplicatesCheck.checked ? 1 : 0 }`

                // return with the prepared url
                return url
            }

            // build pagination
            const buildPagination = (meta) => {
                // set the counts to show beneath the table
                totalCount.innerHTML = meta.total
                displayedCount.innerHTML = meta.per_page
            }

            // populate the table with the response data
            const populateTable = (results) => {
                // clear the table body
                resultsTableBody.innerHTML = ''

                // loop through all the data
                results.forEach((result) => {
                    // add the new row to the table
                    let row = resultsTableBody.insertRow()

                    // add the cells for the first and last names
                    addCell(row, result.firstName)
                    addCell(row, result.lastName)
                })
            }

            // add a cell to a table row
            const addCell = (row, content) => {
                // insert the first cell for the rwo
                const cell = row.insertCell()

                // prepare the text for the cell
                const text = document.createTextNode(content)

                // add the text to the cell
                cell.appendChild(text)
            }
        </script>
    </body>
</html>
