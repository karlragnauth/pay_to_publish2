Commerce License Pay To Publish Documentation
--------------------------------------------

About
-----
This module provides a License type plugin for enabling the purchase of license options
during content creation, which limit viewer access based on role or user id.

Access control via acl's are defined in a hidden permissions_by_term taxonomy field
controlled by the purchased license.

Viewer access restrictions remain independent from normal content publishing
controls. The content creator can publish or unpublish their content as per usual
regardless of licensing status, which is summarised on the content editing form along
with relist options.

Alternative cart and checkout views are included for displaying purchased license
information during checkout.

Installation
------------
Install as you would normally install a contributed Drupal module. Visit
https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules for
further information.

Dependencies
------------
Depends upon the 'permissions_by_term' module to manage acl access control on the
target content via a taxonomy field controlled by the license.
(See https://www.drupal.org/project/permissions_by_term) 

Configuration
-------------
1. Add at least two taxonomy terms defining your desired access control options.
   (Go to admin/structure/taxonomy/manage/pay_to_publish/overview).

   Suggested configuration: Define two options: One as only accessible to the content
   creator and site administrators (Unpublished) and the other accessible to all site
   users (Published).

   a) Click 'Add term'.
   b) Set the Name to 'Unpublished'.
   c) Only check Allowed roles: Administrator.
   d) Set a second term with the Name 'Published'.
   e) Check all Allowed roles: Anonymous user, Authenticated user, Administrator.

2. Add a store if you have not already done so.
   (Go to /store/add).

3. Add products using the 'Pay to publish' product type. A Title of 'Pay to publish' is recommended. Ensure your new product type is published.
   (Go to /admin/commerce/products).

4. Edit your product and add product variations.
   (Go to /admin/commerce/products).

   a) In the product variations settings select the 'Pay to Publish' License Type.

   b) Leave the machine field name on target entity as the default option 
      (field_pay_to_publish), unless you would like this product License to control a
      different field on the target content-type.

      This field is used to reference the 'permissions_by_term' taxonomy vocabulary
      suggested above or another field of your choosing.

      * Be careful, your site will fail if this field does not exist or is set
        incorrectly. Look it up under Structure->Content types if unsure.

   c) Leave the value to set on target field as the default option '2', unless you
      have reason for an alternative. This is the value this license will set upon
      the target entity field while active. For the suggested taxonomy defined above
      this will set the field to option '2' from the taxonomy list (Published).

      If the license is revoked or cancelled, this field will be set to the default
      value defined by the target entity type. We will be defining this a 1
      (Unpublished).

      * Be careful, your site will fail if an incompatible value is specified. eg. You
        must specify the index value for taxonomy list options, not the label.

   d) Select 'Rolling interval' for the License Expiration.

   e) Choose you desired interval before the license expires. ie. 1 week.

   f) Ensure your variation is published and click 'Save'.
       Rinse and repeat creating more product variation until all of your desired
       listing options are defined.

5. Add a content type to be controlled by a Pay to Publish license. Define as you
   normally would.

   a) Under the 'Commerce Pay to Publish' settings, select the available product
      license options available for purchase on this content type.

   b) Click 'Save and manage fields'. Then click 'Add field'.

   c) If 'Entity Reference: field_pay_to_publish' is already available under
      'Re-use an existing field', select that. Click 'Save and continue'.

      -Or-

      Select a new field type of Reference Content. Set the Label to 'Pay to publish'.
      Fields defined with a machine name of 'field_pay_to_publish' are hidden by
      default and only made visible to site administrators.

      Select 'Taxonomy term' for 'Type of item to reference' and set the allowed number      of values to '1'. Click 'Save field settings'

   d) Enter a label. ie. 'Pay to publish' and select 'Required field'.

   e) Set the Default Value to 'Unpublished'.

   f) Select 'Default' for your Reference method and select the 'Pay to publish'
      vocabulary. Click 'Save settings'.

   g) Click on 'Manage form display' and move the 'Pay to publish' field to under
      the title, so it will appear for administrators alongside the Product list
      options. Disable the field under 'Manage display' so it is not visible to users.

6. Go to /admin/people/permissions and under 'Commerce License Pay to Publish'
   set your desired administrator access.

   'Relist Commerce Pay to Publish' enables the chosen role to relist content by
   purchasing a new list option.

   'Administer Commerce Pay to Publish' allows administrators to skip the checkout when
   creating licensed content-types.

Your site is now ready to provide purchasable licensed content.
Go to /node/add and select your new licensed content type for publication.

Acknowledgements
---------------
Concept inspired by (and come code ported from):

Drupal 7: commerce_node_checkout
(https://www.drupal.org/project/commerce_node_checkout)

Drupal 8: commerce_license_entity_field
(https://www.drupal.org/project/commerce_license_entity_field)

Support/Customizations
----------------------
Please consider helping others as a way to give something back to the community
that provides Drupal and the contributed modules to you free of charge.

For paid support and customizations of this module contact the project maintainer (Dan Greenman) through this contact form:

https://www.drupal.org/user/2722223/contact

Credits
-------
Thank you to Tom Davies for his encouragement and support during this project work.

Donations
---------
If you value this module please consider sending a donation to show your appreciation:

Monero:
44KTVNFzSmC12srxKgxvCEbmUXSzjNmT1NAHRpF9tuhvCDMpsimTZerAxPr4pNrtT7EjqN45WKerrAh1K9UgKayR9ogksYm

Bitcoin:
1E56meMLWhTsXK6NCnNgE1j7m6cGXC3kKb

Or just send me a message. I'd love to hear from you.
https://greenman-it.pw/contact

I am a freelancer for hire, looking for work.
