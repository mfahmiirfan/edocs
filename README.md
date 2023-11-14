# E-DOCUMENT

## Description

This project serves as a centralized repository within a corporate setting, housing a wide array of documents such as forms, procedures, policies, certificates, and reports. It empowers employees to swiftly search, access, and download necessary documents. Featuring a robust search function, it enables seamless document retrieval, ensuring access permissions are in place for specific documents, thereby streamlining the document management process while maintaining security and accessibility for authorized personnel.

⚠️ **Disclaimer**: This is not a complete project. Some code is not included and is intended solely for demonstration purposes as part of a portfolio. You can find the excluded files by checking the [.gitignore](.gitignore) file.

## Technologies Used

- **Backend**: Codeigniter 3 (RESTful), Node.js for background notification scheduling
- **Frontend**: jQuery, Bootstrap
- **Database**: Microsoft SQL Server
- **Authentication**: JWT
- **Pagination**: SQL seek method for paginated tables
- **Search**: ElasticSearch for robust search functionality

## Features

- **CRUD Operations for Documents**: Enables document uploading of various types like doc, xls, ppt, pdf. Users can update, delete, and set document expiration if required.
- **Authorization Management**: Specifies document access, determining eligible departments or individuals. Defines user roles for viewing, downloading, adding, editing, deleting, or a combination of these actions.
- **Document Preview**: Allows document preview without downloading.
- **Document Download**: Permits downloading documents to the local storage.
- **Search Capability**: Utilizes Elasticsearch to facilitate user-friendly document searches.
- **Notification Feature**: Sends notifications through the interface or email when documents approach their expiration date. Notifications are sent regularly in the final month, starting from monthly and increasing to weekly in the last month (e.g., H-90 to H-7).
- **Activity Log**: Records user activity, tracking views, downloads, additions, updates, and deletions.

## Screenshots

### 1. CRUD Operations for Documents
<p align="center">
  <img src="screenshots/crud-document.PNG" alt="Crud document" width="100%">
  <br>Image 1.1. - Crud document
</p>
<p align="center">
  <img src="screenshots/add-document.PNG" alt="Add document" width="100%">
  <br>Image 1.2. - Add document
</p>
<p align="center">
  <img src="screenshots/delete-document.PNG" alt="Delete document" width="100%">
  <br>Image 1.3. - Delete document
</p>

### 2. Authorization Management
<p align="center">
  <img src="screenshots/choose-departments.PNG" alt="Choose departments" width="100%">
  <br>Image 2.1. - Choose departments
</p>
<p align="center">
  <img src="screenshots/choose-role.PNG" alt="Choose role" width="100%">
  <br>Image 2.2. - Choose role
</p>

### 3. Document Preview
<p align="center">
  <img src="screenshots/preview-document.PNG" alt="Preview document" width="100%">
  <br>Image 3. - Preview document
</p>

### 4. Document Download
<p align="center">
  <img src="screenshots/download-document.PNG" alt="Download document" width="100%">
  <br>Image 4. - Download document
</p>

### 5. Search Capability
<p align="center">
  <img src="screenshots/search-document.PNG" alt="Search document" width="100%">
  <br>Image 5. - Search document
</p>

### 6. Notification Feature
<p align="center">
  <img src="screenshots/interface-notifications.PNG" alt="Interface notifications" width="100%">
  <br>Image 6.1. - Interface notifications
</p>
<p align="center">
  <img src="screenshots/email-notifications.PNG" alt="Email notifications" width="100%">
  <br>Image 6.2. - Email notifications
</p>

### 7. Activity Log
<p align="center">
  <img src="screenshots/activity-log.PNG" alt="Activity log" width="100%">
  <br>Image 7. - Activity log
</p>

## Support and Contact
For any support or feedback, please contact us at mfahmiirfan@gmail.com.
