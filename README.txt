.ams store DATABASE README FILE


Alexey Indeev
Kevin Stark
Jon Jeffery
Allan Bisnar

FUNCTIONALITY

---------------INDEX PAGE------------------------

			== Index ==
	This is the first page that the user will see. They will see a simple welcome message and be given to a choice to either login or register.
	

			== Register Account ==
	This page allows a custoemr to sign up for our online store. It looks very similar to register pages to other online services.
	It asks for a username, password, confirm password field, name, address and phone.
	
	When the users submit the form, ee first check if the username is not empty and then we check that is doesn't equal to clerk or manager.
	We then check if this particular username already exists in our Customer table.
	
	If the username passes all these checks we make sure that passwords match and store the user in our Customer table. (Address, Name, and Phone are not validated)
	The password is stored in the database as a hash. (We chose md5 for proof of concept rather than it's security. We are waware that it is almost the same as storing the passwords in cleartext)
	
	After the registration is complete we assign a PHP session to the user with CUSTOMER access level and redirect them to the Product search page
	
			== Login ==
			
	On the login page the user is asked to enter their username and password. They are then redirected to their base index page
	(Customer is shown the search page, clerks will see the "purchase/return" page, and managers will see their page)
	
	If the user click on the "Manager/Clerk Login" button below a dropdown box will appear and give them the choice to login as an employee.
	The clerk/manager username and password are hardcoded.
	
	Username: clerk			Username: manager
	Password: password		Password: password

---------------HEADER----------------------------
	
	Every page will display the header which contains the store name (link to index page) and username dropdown
	Depending on the user access, we will also display links to lower level views.
	
	ex. a manager can see the manager, clerk, and customer views
		a clerk can see the clerk and cusotmer views
		
	All users will also see the logout button.
	
---------------CUSTOMER PERSPECTIVE--------------

			== Search ==
	
	On this page the user will be able to search by any term they want to. Searching with multiple terms will narrow down the results
	Search terms can be added and removed. There is no limit on the amount of search terms, but selecting the same field twice will result in an error. 

	In the search results the user can either add the item to their cart or they will see a red "out of stock" message.
	Clicking add to cart will redirect them to the shopping cart page
	
	No employee account will be allowed past this point (clerk/manager) to prevent them from placing online purchases
	If they wish to do so, then they will have to create a personal account through the register page.
	
			== Cart ==
	
	The cart information is stored using PHP session variables (so if the user log's out, the information will be lost)
	It displays all the items in cart and allows the customer to update the quantities.
	
	Pressing the "checkout" button will move the user to the checkout page.
	
			== Checkout ==
			
	The checkout page also displays all items in cart, but it does not allow the user to update item quantity.
	On this page the customer eill enter their credit card information and the press purchase.
	
	Assertions done in checkout:
		- Cart must have at least one item
		- The credit card expiry date has to be in the furture
		- credit card must be over 14 digits long (not a proper check, but using the proper algorith would complicate testing)
		- must be one of visa, mastercard, american express.
		
	When the purchase is completed a success message is printed with the order number (recieptId) and the estimated number of days that the delivery will take.
 
 
--------------CLERK PERSPECTIVE------------------
			== Process Purchase ==
	The clerk will search for an item in the store by its (valid) UPC, and the quantity that the customer wishes to purchase. The item will be added to the cart.
	If no quantity is selected the default value is 1. 

	The clerk can add as many items as the customer wants, as long as there is sufficient stock. The current contents of the shopping cart 
	are displayed below the search input, as is the current purchase price. 

	Once the customer is satisfied with the order, the clerk will press the proceed to payment button.
	
	The payment page displays the final shopping cart contents as well as the final sale price. If the customer changes their mind, the clerk can cancel the purchase
	and return to the home page.

	The purchase can be completed either by cash or credit card. If credit card is selected, the clerk will enter the card information in the appropriate forms. 
	Once the information is fully entered the clerk can proceed with rthe card purchase. At this point the clerk should debit the customer's card on the external card terminal. 

	If the card isn't validated, the clerk will have a chance to cancel the card transaction. The customer will have a chance to pay with cash if they choose. If they
	prefer not to, the purchase will be cancelled.

	Once the purchase is confirmed a page containing the receipt will be shown. This page includes all the final sale information, as well as the receipt ID.

	
			== Process Return == 
	The clerk will enter a valid receipt ID, an item that was purchased on the original sale, and a quantity equal to or lower than the original purchase quantity.

	If the quantity is above the original quantity, or if there have been previous returns that would increase the total return quantity to above the original purchase quantity, 
	an error message will be displayed and the purchase will not complete.

	If the information entered is all correct the clerk will be instructed to refund the customer for the appropriate amount using the purchase method originally used.
	A new return will be created and the items automatically reentered into the database. 	


--------------MANAGER PERSPECTIVE---------------
			== Add New Item To Database==
	This query allows the Manager add a valid new Item to the Database.

	First, it makes sure the form being filled is set, then form input is checked.

	There must be a provided integer in the UPC field, or else will print out an error call, since it is a primary key.

	Then we query the Item database to check for any Items with the given UPC, 
	there there gives a row greater than 0, then that item already exists and terminates yielding an error to update or add to existing stock

	then the function checks for other important restraints:
		The type must be either a cd or dvd, or else there will be an error call
		The category must be either rock, pop, rap, country, classical, new age or instrumental. Or else an error message will present

	Company, price, quantity, year are all optional and can be null when you press the submit button
	doing so will fill a NULL value for company, while price,quantity and year are all set to 0 by default.

	if you try to add a new item with the same UPC number through add new item, it will yeild an error telling the user to
	add to existing Item or Update the Item's fields through the Update Item option

				==  Add to Existing Item==

	This allows the manager to update the price and stock of an existing object.
	First we check to make sure the object exists filled in the query
	There is a check to make sure a quantity and UPC value is provided or else an error message is sent
	
	Then the function checks to see if price is set
	if so, a Query is made = UPDATE Item set stock = stock + stock_input AND price = new price
	WHERE UPC = input UPC2 value
	
	If no price is provided only update stock = stock + input_stock query runs

Remove Item
	Remove Item checks to see if the UPC exists in the database, if so, it deletes the entire tuple

Update Inventory(modify fields)
This class contains many forms each for the different fields that you can modify.

When a form is filled out, and submitted, a helper if statement checks the paramenters to make sure the right form is processed and checks the input field parameters are valid
if it passes the check, a call to the update switch statement with the corresponding condition is processed
calling an update to Item for the corresponding field.

Since we are changing fields, all form values for the expected update must be filled out or an error is printed out


							= Daily Sales Report = 

This page allows managers to input a date and receive a table of all sales made that day. The sales are listed by category of music and totals for those categories are listed beneath each.  The daily total is tallied and displayed at the bottom of the page. 

The system requires the user to input a date from any point until the current date. An error will be displayed if a user attempts to choose a date in the future (e.g. next week). There are no bounds on how far back in time a user may request a report, but if there are no sales listed in the system for that date, the user will receive a warning that the system return no sales. The system does not preserve dates on update, meaning that if you input a date and press 'Submit', the date will reset to the default value and the user must input the date again to receive another report.

							= Top Selling Items =

A manager may use this page to request the most popular items in the store for an enclosed period. The system will output a table listing these popular items from most to least popular for the user  along with the number of copies those items have sold. Note that the system regards popularity in terms of copies sold. 

For this system to successfully output a result for the user, two of this pages fields must be filled out. There are three inputs for the user to fill. There is a From: field which specifies the date the user wishes the system to start collecting data for. Similarly, the To: field allows the user to specify when they would like the system to stop collecting data. Finally, the user must specify the number of top selling items they would like to be listed in the report. If the user leaves this field blank, the top five items will be returned by default. 

Errors may arise if the user attempts any of the following:

	input a date in either date field which is further in time than the current date
	input a start date that is further in the future the end date
	leave one of the date fields blank
	input zero or negative number in the 'Top Items' field

							= Update Order = 

The store requires the manager to be able to update the status of online orders. Namely, it allows the manager to set the delivery date for any purchase. This ensures that all online orders will have an accurate expected date when made by a customer because completed orders will be periodically updated using this functionality. 

To complete a transaction using this page, the manager must first obtain the receipt ID for the purchase they wish to update. After entering a receipt ID into the first field, a table will pop up showing the details for the purchase, including the expected date for the order. At the bottom of the screen, there will be a date field which the  user can use to update the delivery date. Pressing 'Submit' will update the delivery date and the page will give the user a green notice that the system was successfully updated.

The user will receive a specific error message for any of the following:

	input an invalid receipt ID number
	submit either field without a value inputted
	submit a date after the current date

Note that the system does not restrict the user from inputting a date before the purchase date. So the user must be mindful when inputting the date.

