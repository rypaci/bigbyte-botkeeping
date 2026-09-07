<!DOCTYPE html>

<html>
<head>
    <title>Invoice to EverAccountable</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @media all {
        
        * { font-family: arial; font-size: 12px; -webkit-print-color-adjust: exact; }
        table { border-collapse: collapse; width: 100%; }
        .table1 { border: 1px solid #ddd; }
        .table1 th { text-align: left; border-bottom: 1px solid #ddd; color: #555; background-color: #f9f9f9; }
        .table1 td { border-bottom: 1px solid #ddd; }
        .table1 th, td { padding: 5px 10px; }
        .table1 .description { width: 77%; }
        .table1 .hours { width: 5%; }
        .table1 .rate { width: 8%; }
        .table1 .amount { width: 10%; }
        .table1 .amount, .table1 .rate, .table1 .hours { text-align: right; }
        .table1 td:nth-child(2), .table1 td:nth-child(3), .table1 td:nth-child(4) { text-align: right; }
        tr.total { font-weight: bold; background-color: #f9f9f9; }
        
        h4 { font-size: 15px; margin: 0; }
        .invoice { font-size: 28px; }
        .field, .value { width: 20%; vertical-align: top; }
        .field { text-align: right; padding-right: 0px; }
        .my-info { width: 60%; }
        .spacer { min-height: 10px; }
        .table0 { margin-bottom: 10px; }
        .due { border: 1px solid #ddd; padding: 5px; text-align: center; }
        .due-amount { font-size: 15px; font-weight: bold; }
        
        table tr { page-break-inside: avoid; }
        
        }
    </style>
</head>

<body>
    <table class="table0">
        <tr>
        <td class="my-info">
<h4>Amiel E. Barino</h4>
Prk 12, Brgy 76-A, St. John, Bucana, Davao City<br>
amilbar2009@gmail.com<br><br>

Bill To:<br> 
Ever Accountable<br>
hr@everaccountable.com
        </td>
        
        <td class="field">
<br><br><br>
Invoice Date:<br>
        </td>
        
        <td class="value">
<div class="invoice">INVOICE</div>
<div class="spacer"> </div>
June 01, 2017<br><br>
<div class="due">Amount Due:<br><span class="due-amount">$210.00</span></div>
        </td>
        </tr>
    </table>
    
    <table class="table1">
        <thead><tr><th class="description">Description</th><th class="hours">Hours</th><th class="rate">Rate</th><th class="amount">Amount</th></tr></thead>
        <tbody>
        <tr>
            <td>
MAY04 Progress:<br>
 - deploy latest updates to live site.<br>
 - review on angular factory and service<br>
 - research on communicating/passing data between controllers.
            </td>
            <td>4</td>
            <td>$6.00</td>
            <td>$24.00</td>
        </tr>
        
        <tr>
            <td>
MAY07 Progress:<br>
 - started working on restructuring main service. [in process]
            </td>
            <td>3</td>
            <td>$6.00</td>
            <td>$18.00</td>
        </tr>
        
        <tr>
            <td>
MAY07 Progress:<br>
 - started working on restructuring main service. [in process]
            </td>
            <td>3</td>
            <td>$6.00</td>
            <td>$18.00</td>
        </tr>
        
        <tr>
            <td>
MAY07 Progress:<br>
 - started working on restructuring main service. [in process]
            </td>
            <td>3</td>
            <td>$6.00</td>
            <td>$18.00</td>
        </tr>
        
        <tr>
            <td>
MAY07 Progress:<br>
 - started working on restructuring main service. [in process]
            </td>
            <td>3</td>
            <td>$6.00</td>
            <td>$18.00</td>
        </tr>
        
        <tr>
            <td>
MAY07 Progress:<br>
 - started working on restructuring main service. [in process]
            </td>
            <td>3</td>
            <td>$6.00</td>
            <td>$18.00</td>
        </tr>
        
        <tr>
            <td>
MAY07 Progress:<br>
 - started working on restructuring main service. [in process]
            </td>
            <td>3</td>
            <td>$6.00</td>
            <td>$18.00</td>
        </tr>
        
        <tr>
            <td>
MAY09 Progress:<br>
 - continue on restructuring mainservice.<br>
 - Add comments and remove unused variables and functions in services js.<br>
 - Revise function getAncestryGeneration to accept person object as param instead of order.<br>
 - Convert the code from assoc. array to object for better code reading.<br>
 - Move function getUnclesAndAuntsRelation out from fetchUnclesAndAunts function to avoid reinitializing in each function call.<br>
 - Include generate ancestry relationship in service js.<br>
 - Revise personRelation to use new function getAncestryRelation.
 - deployed to test site
            </td>
            <td>9</td>
            <td>$6.00</td>
            <td>$54.00</td>
        </tr>
        
        <tr>
            <td>
MAY16 Progress:<br>
 - install gulp packages for minifying css and js. <br>
 - search how the packages work.<br>
 - moved the raw css and js files to gulp folder. <br>
 - create 2 tasks to run 2 version of script for local and production js compression.<br>
 - fixed css background URL changed when doing css compression. <br>
 - add more files in gitignore to exclude from pushing. 
            </td>
            <td>8</td>
            <td>$6.00</td>
            <td>$48.00</td>
        </tr>
        
        <tr>
            <td>
MAY22 Progress:<br>
 - Prepare the files to push to repo, group the files to give a clearer description on each commit.<br>
 - Add more files to ignore tracking.<br>
 - Fix - Error on minified script when clicking the button to show About Modal.<br>
 - Add instructions to setup gulp in README.md.<br>
 - Prepare documentation based from the reply in email. [in process]
            </td>
            <td>4</td>
            <td>$6.00</td>
            <td>$24.00</td>
        </tr>
        
        <tr>
            <td>
MAY27 Progress:<br>
 - update repo: Separate js src for local, test and production.<br>
 - deploy the latest update to test server.<br>
 - make a documentation.
            </td>
            <td>4</td>
            <td>$6.00</td>
            <td>$24.00</td>
        </tr>
        
        <tr>
            <td>
MAY27 Progress:<br>
 - update repo: Separate js src for local, test and production.<br>
 - deploy the latest update to test server.<br>
 - make a documentation.
            </td>
            <td>4</td>
            <td>$6.00</td>
            <td>$24.00</td>
        </tr>
        
        <tr>
            <td>
MAY27 Progress:<br>
 - update repo: Separate js src for local, test and production.<br>
 - deploy the latest update to test server.<br>
 - make a documentation.
            </td>
            <td>4</td>
            <td>$6.00</td>
            <td>$24.00</td>
        </tr>
        
        <tr>
            <td>
MAY27 Progress:<br>
 - update repo: Separate js src for local, test and production.<br>
 - deploy the latest update to test server.<br>
 - make a documentation.
            </td>
            <td>4</td>
            <td>$6.00</td>
            <td>$24.00</td>
        </tr>
        
        <tr>
            <td>
MAY27 Progress:<br>
 - update repo: Separate js src for local, test and production.<br>
 - deploy the latest update to test server.<br>
 - make a documentation.
            </td>
            <td>4</td>
            <td>$6.00</td>
            <td>$24.00</td>
        </tr>
        
        <tr>
            <td>
MAY27 Progress:<br>
 - update repo: Separate js src for local, test and production.<br>
 - deploy the latest update to test server.<br>
 - make a documentation.
            </td>
            <td>4</td>
            <td>$6.00</td>
            <td>$24.00</td>
        </tr>
        
        <tr>
            <td>
MAY27 Progress:<br>
 - update repo: Separate js src for local, test and production.<br>
 - deploy the latest update to test server.<br>
 - make a documentation.
            </td>
            <td>4</td>
            <td>$6.00</td>
            <td>$24.00</td>
        </tr>
        
        <tr>
            <td>
MAY31 Progress:<br>
 - update repo: Fix - comments page layout.<br>
 - deployed in production<br>
 - search more about FB share button why some sites are able to share dynamic page.<br>
 - checked some tasks on trello.
            </td>
            <td>3</td>
            <td>$6.00</td>
            <td>$18.00</td>
        </tr>
        
        <tr class="total">
            <td></td>
            <td colspan="2">Amount Due:</td>
            <td>$210.00</td>
        </tr>
        </tbody>
    </table>
</body>
</html>

