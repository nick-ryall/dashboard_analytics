# Google Analytics Dashboard Panel
 
* Version: 1.0
* Author: Nick Ryall
* Build Date: 2011-03-14
* Requirements: Symphony 2.2, Dashboard extension

## Purpose
To provide a Dashboard summary screen for users. Dashboard "panels" can contain any information. This extension provides the framework for building a Dashboard, and provides four basic panel types. Other extensions can provide their own panel types.

## Installation
 
1. Upload the 'dashboard_analytics' folder in this archive to your Symphony 'extensions' folder
2. Enable it by selecting "Dashboard Analytics" in the list, choose Enable from the with-selected menu, then click Apply
3. Navigate to the Dashboard from the "Dashboard" link in the primary navigation and select "Dashboard Analytics" from the "Create New" list

## Usage

You need to supply your Google Analytics user account `Email` and `Password`. The `Profile ID` is the ID of the website profile.

For the OLD VERSION analytic page this can be found in the URL of your site report in Analytics, for example:

	https://www.google.com/analytics/reporting/?id=123456789

For the NEW VERSION analytic page it is the number at the end of the URL starting with p

	https://www.google.com/analytics/web/#home/a11345062w43527078pXXXXXXXX/ 

Please note that your login details will be stored as plain text.