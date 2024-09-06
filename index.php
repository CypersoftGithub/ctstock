<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Design Data Viewer</title>
    <style>
        #data-table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #data-table td, #data-table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #data-table tr:nth-child(even) {background-color: #f2f2f2;}

        #data-table tr:hover {background-color: #ddd;}

        #data-table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #04AA6D;
            color: white;
        }

        .search-container {
            margin-bottom: 20px;
        }

        .search-container input[type="text"] {
            padding: 8px;
            margin-right: 8px;
            font-size: 16px;
        }

        .search-container button {
            padding: 8px 16px;
            font-size: 16px;
            background-color: #04AA6D;
            color: white;
            border: none;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #45a049;
        }

        /* Loader Styles */
        .loader {
            border: 8px solid #f3f3f3;
            border-radius: 50%;
            border-top: 8px solid #3498db;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            display: none; /* Initially hidden */
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <h1>Design Data Viewer</h1>

    <!-- Search Input and Buttons -->
    <div class="search-container">
        <label for="designname">Filter by Design Name:</label>
        <input type="text" id="designname" placeholder="Enter design name">
        <button onclick="fetchData()">Search</button>
        <button onclick="clearSearch()">Clear</button>
    </div>

    <!-- Loader Indicator -->
    <div class="loader" id="loader"></div>

    <!-- Table to Display Data -->
    <table id="data-table">
        <thead>
            <tr>
                <th>Design Name</th>
                <th>First Of Design Name C</th>
                <th>Prdt Name</th>
                <th>Brand Name</th>
                <th>Size Name</th>
                <th>Cat Name</th>
                <th>Batch Name</th>
                <th>Shade Name</th>
                <th>Mfg Status</th>
                <th>First Of Design Act</th>
                <th>Sum Of G1</th>
                <th>Sum Of G2</th>
                <th>Sum Of G3</th>
                <th>Sum Of G4</th>
                <th>Sum Of Gtot</th>
                <th>First Of Bt Box Wt</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be populated here by JavaScript -->
        </tbody>
    </table>

    <script>
        // Fetch data from the server and update the table
        async function fetchData() {
            const designname = document.getElementById('designname').value.trim();
            
            // Create the URL for the API call
            const url = designname ? `fetch_data.php?designname=${encodeURIComponent(designname)}` : 'fetch_data.php';
            
            // Show the loader
            document.getElementById('loader').style.display = 'block';

            try {
                // Fetch data from the PHP script
                const response = await fetch(url);
                const data = await response.json();

                // Hide the loader after fetching data
                document.getElementById('loader').style.display = 'none';

                // Get the table body element
                const tableBody = document.querySelector('#data-table tbody');
                tableBody.innerHTML = ''; // Clear previous data

                // Populate the table with new data
                data.forEach(row => {
                    const tr = document.createElement('tr');
                    for (const key in row) {
                        if (row.hasOwnProperty(key)) {
                            const td = document.createElement('td');
                            td.textContent = row[key];
                            tr.appendChild(td);
                        }
                    }
                    tableBody.appendChild(tr);
                });
            } catch (error) {
                console.error('Error fetching data:', error);
                // Hide the loader in case of an error
                document.getElementById('loader').style.display = 'none';
            }
        }

        // Clear the search input and fetch all data
        function clearSearch() {
            document.getElementById('designname').value = ''; // Clear input field
            fetchData(); // Fetch all data
        }

        // Initial fetch to display all data on page load
        fetchData();
    </script>
</body>
</html>
