<div id='propertiesAdd'>
    <button id='propertiesShowAdd'>Show Add New Property</button>
    <div id='propertiesAddNewProperty'>
        <div>
            <label for="#propertiesAddID">Property ID</label>
            <input id='propertiesAddID' type='text' />
        </div>

        <div>
            <label for="#propertiesAddName">Name</label>
            <input id='propertiesAddName' type='text' />
        </div>

        <div>
            <label for="#propertiesAddDescription">Description</label>
            <textarea id='propertiesAddDescription' rows='4'></textarea>
        </div>

        <div>
            <label for="#propertiesAddAddress">Address</label>
            <textarea id='propertiesAddAddress' rows='4'></textarea>
        </div>

        <div>
            <label for="#propertiesAddPrice">Minimum Nightly Price</label>
            <input id='propertiesAddPrice' type='number' />
        </div>

        <div>
            <label for="#propertiesAddFee">Management Fee</label>
            <input id='propertiesAddFee' type='number' />
        </div>

        <div>
            <label for="#propertiesAddImageURL">Image URL</label>
            <input id='propertiesAddImageURL'></input>
        </div>

        <div>
            <button id='propertiesAddButton'>Add New Property</button>
        </div>

        <div id='orText'>
            OR
        </div>

        <form id='propertiesDropzone' class='dropzone' action='php/properties/uploadpropertyimage.php'>
            <div class="fallback">
                <input name="file" type="file" />
            </div>
        </form>
    </div>
</div>

<table id='propertyTable'>
    <thead>
        <tr>
            <th>
                Property
            </th>
            <th>
                Name
            </th>
            <th>
                Description
            </th>
            <th>
                Address
            </th>
            <th>
                Management Fee
            </th>
            <th>
                Minimum Nightly Price
            </th>
        </tr>
    </thead>
    <tbody>
        <!-- Properties dynamically added here -->
    </tbody>
</table>

<table id='propertyMobile'>
    <tbody>
        <!-- Properties dynamically added here -->
    </tbody>
</table>

<div id='propertySubpage' class='row'>

</div>

<!-- Modal Code -->
<div id='newPropertyImageContainer' class="modal" style="display:none">
    <form id='newPropertyImageDropzone' class='dropzone' action='php/properties/uploadpropertyimage.php'>
        <div class="fallback">
            <input name="file" type="file" />
        </div>
    </form>

    <button id='newPropertyImageButton'>Change Image</button>
</div>
