<div id='documentsAdd'>
    <button id='documentsShowAdd'>Show Add New Document</button>
    <div id='documentsAddNewDocument'>
        <!--
        <div>
            <label for="#documentsAddName">Name</label>
            <input id='documentsAddName' type='text' />
        </div>
        -->

        <div>
            <label for="#documentsAddPropertyID">Property</label>
            <select id='documentsAddPropertyID'></select>
        </div>

        <div>
            <label for="#documentsAddMonth">Month</label>
            <select id='documentsAddMonth'>
                <option value="January">January</option>
                <option value="February">February</option>
                <option value="March">March</option>
                <option value="April">April</option>
                <option value="May">May</option>
                <option value="June">June</option>
                <option value="July">July</option>
                <option value="August">August</option>
                <option value="September">September</option>
                <option value="October">October</option>
                <option value="November">November</option>
                <option value="December">December</option>
            </select>
        </div>

        <div>
            <label for="#documentsAddYear">Year</label>
            <select id='documentsAddYear'>
                <option value="2015">2015</option>
                <option value="2016">2016</option>
                <option value="2017">2017</option>
                <!-- <option value="2018">2018</option> -->
            </select>
        </div>

        <div>
            <label for="#documentsAddNotes">Notes</label>
            <textarea id='documentsAddNotes' lines='4'></textarea>
        </div>

        <div>
            <label for="#documentsAddFilename">Documents uploaded via FTP</label>
            <select id='documentsAddFilename'></select>
        </div>

        <div>
            <button type='submit' id='documentsAddButton'>Add New Document</button>
        </div>

        <div id='orText'>
            OR
        </div>

        <form id='documentsDropzone' class='dropzone' action='php/documents/uploaddocument.php'>
            <div class="fallback">
                <input name="file" type="file" />
            </div>
        </form>
    </div>
</div>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#documentsAll">All</a></li>
    <li><a data-toggle="tab" href="#documents2016">FY 2016</a></li>
    <li><a data-toggle="tab" href="#documents2017">FY 2017</a></li>
    <!-- <li><a data-toggle="tab" href="#documents2018">FY 2018</a></li> -->
</ul>

<?php
$tableHTML = "
<table>
    <thead>
        <tr>
            <th>
                Document
            </th>
            <th>
                Property
            </th>
            <th>
                Month
            </th>
            <th>
                Year
            </th>
            <th>

            </th>
            <th>

            </th>
            <th>

            </th>
        </tr>
    </thead>
    <tbody>
        <!-- Documents dynamically added here -->
    </tbody>
</table>
";
?>

<div class="tab-content">
    <div id="documentsAll" class="tab-pane fade in active">
        <?php echo $tableHTML; ?>
    </div>
    <div id="documents2016" class="tab-pane fade">
        <?php echo $tableHTML; ?>
    </div>
    <div id="documents2017" class="tab-pane fade">
        <?php echo $tableHTML; ?>
    </div>
    <!-- <div id="documents2018" class="tab-pane fade">
        <?php //echo $tableHTML; ?>
    </div> -->
</div>
