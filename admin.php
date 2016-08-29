<table id='adminContractExpiryEmailTimeTable'>
    <tr>
        <td><strong>Set Expiry Email Notification Time</strong></td>
    </tr>
    <tr>
        <td><input type='number' id='adminContractExpiryEmailTime' /> <span>months</span></td>
    </tr>
    <tr>
        <td><button onclick='saveContractExpiryEmailTime()'>Save</button></td>
    </tr>
</table>

<table>
    <tr>
        <td>
            <strong>Create new user</strong>
        </td>
    </tr>
    <tr>
        <td>
            Username
        </td>
        <td>
            First Name
        </td>
        <td>
            Last Name
        </td>
    </tr>
    <tr>
        <td>
            <input id='adminNewCustomerUsername' type='text' />
        </td>
        <td>
            <input id='adminNewCustomerFirstName' type='text' />
        </td>
        <td>
            <input id='adminNewCustomerLastName' type='text' />
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <button id='adminCreateNewCustomerButton'>Create New Customer</button>
        </td>
    </tr>
</table>

<div class='usersLabel'>
    <strong>Users</strong>
</div>
<table id='userTable'>
    <thead>
        <tr>
            <th>Owner Name</th>
            <th>Email Address</th>
            <th>Properties</th>
            <th>Last Login</th>
            <th>Status</th>
            <th colspan="2">Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
