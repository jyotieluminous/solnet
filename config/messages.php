<?php

#ROLE
define('ROLE_ADD_SUCCESS','Role added successfully');

define('ROLE_ADD_ERR','Role not added. Please try again');

define('ROLE_UPDATE_SUCCESS','Role updated successfully');

define('ROLE_DELTED_ERR','Role not deleted. Please try again');

define('ROLE_DELTED_MSG','Role deleted successfully');

define('ROLE_NAME_ERR','Role name should not be blank');

define('ROLE_NAME_UNIQUE_ERR','Role name should be unique');

#CUSTOMER
define('CUSTOMER_DELETE_SUCCESSFULL','Customer deleted successfully.');
define('CUSTOMER_CREATE_SUCCESSFULL','Customer created successfully.');
define('CUSTOMER_UPDATE_SUCCESSFULL','Customer updated successfully.');
define('PENDING_ACTIVATE_SUCCESSFULL','Customer activated successfully.');
define('CUSTOMER_DISCONNECT_SUCCESSFULL','Customer has been disconnected successfully.');
define('INVOICE_STATUS_SUCCESSFULL','Invoice status has been changed successfully.');

#STATE
define('STATE_CREATE_SUCCESSFULL','State created successfully.');
define('STATE_DELETE_SUCCESSFULL','State deleted successfully.');
define('STATE_UPDATE_SUCCESSFULL','State updated successfully.');
define('STATE_DELETE_RESTRICT','could not delete, as it assign to customer(s).');
define('STATE_STATUS_RESTRICT','Could not change the status, as it assign to customer(s).');


#PACKAGE
define('PACKAGE_CREATE_SUCCESSFULL','Package created successfully.');
define('PACKAGE_DELETE_SUCCESSFULL','Package deleted successfully.');
define('PACKAGE_UPDATE_SUCCESSFULL','Package updated successfully.');

#BANK
define('BANK_CREATE_SUCCESSFULL','Bank created successfully.');
define('BANK_DELETE_SUCCESSFULL','Bank deleted successfully.');
define('BANK_DELETE_RESTRICT','could not be deleted, as it assign to customer(s).');
define('BANK_UPDATE_SUCCESSFULL','Bank updated successfully.');


#BANK DEPOSIT
define('BANK_DEPOSIT_CREATE_SUCCESSFULL','Amount deposited successfully.');
define('BANK_DEPOSIT_CREATE_FAIL','Amount failed to deposite.');
define('BANK_DEPOSIT_DELETE_SUCCESSFULL','Deposit deleted successfully.');
define('BANK_DEPOSIT_DELETE_FAIL','Deposit failed to delete.');
define('BANK_DEPOSIT_UPDATE_SUCCESSFULL','Deposit updated successfully.');

#USER
define('USER_DELETE_SUCCESSFULL','User deleted successfully.');

#BILLING CUSTOMER
define('CUSTOMER_PERSONAL_UPDATE_SUCCESSFULL','Personal details saved successfully.');
define('CUSTOMER_PACKAGE_UPDATE_SUCCESSFULL','Package details saved successfully.');
define('CUSTOMER_BANKE_UPDATE_SUCCESSFULL','Bank details saved successfully.');
define('CUSTOMER_BILLING_DELETE_SUCCESSFULL','Customer deleted successfully.');

#ACTIVATE CUSTOMER
define('CUSTOMER_ACTIVATE_DELETE_SUCCESSFULL','Customer deleted successfully.');

#BANK DEPOSIT
define('PROSOPECT_CREATE_SUCCESSFULL','Prospect added successfully.');
define('PROSOPECT_DELETE_SUCCESSFULL','Prospect deleted successfully.');
define('PROSOPECT_UPDATE_SUCCESSFULL','Prospect updated successfully.');


#CONTRAT REPORT EDIT
define('CONTRACT_REPORT_EDIT_SUCCESSFUL','Contract updated successfully');
define('CONTRACT_REPORT_EDIT_FAIL','Contract failed to update.Try Again.');


#GENEREAL SETTINGS UPDATE
define('SETTING_UPDATE_SUCCESSFULL','Settings updated successfully');
define('SETTING_UPDATE_FAIL','Settings failed to update.Try Again.');


#INVOICE
define('INVOICE_SENT_SUCCESSFULL','Invoice mail sent successfully.');
define('INVOICE_CUSTOM_SUCCESSFULL','Custom invoice generated successfully.');
define('INVOICE_DELETE_SUCCESSFULL','Paid invoice deleted succcessfully.');
define('INVOICE_PAID_SUCCESSFULL','Invoice paid succcessfully.');


?>