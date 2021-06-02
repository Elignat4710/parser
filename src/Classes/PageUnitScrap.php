<?php

namespace App\Classes;

/**
 * Class for scraping the content from the page
 */
class PageUnitScrap
{
    // Page content
    protected $content;
    // Task information
    protected $task;

    /**
     * A builder with the parameters
     *
     * @param object $content
     * @param array $task
     */
    public function __construct($content, $task)
    {
        $this->content = $content;
        $this->task = $task;
    }

    /**
     * Scrapes data from $content
     *
     * @return void
     */
    public function scrap()
    {

        // type
        $type = isset($this->content->find('h2.PropertyTypeIcon-keyword')[0]) ?
            Queue::clearText($this->content->find('h2.PropertyTypeIcon-keyword')[0]->text()) : '';
        
        // address
        $checkAddress = isset($this->content->find('section.HdpAddress-title h2')[0]) ? true : false;
        
        $address = isset($this->content->find('section.HdpAddress-title')[0]) ?
            $this->content->find('section.HdpAddress-title') : '';

        if (!$checkAddress) {
            $addrLine2 = isset($address[0]->find('address')[0]) ?
                Queue::clearText($address[0]->find('address')[0]->text()) : ''; // addr_line_2
        
            $addrLine1 = isset($address[0]->find('h1')[0]) ?
                Queue::clearText($address[0]->find('h1')[0]->text()) : '';
            $addrLine1 = str_replace($addrLine2, '', $addrLine1); // addr_line_1

            $address = $addrLine1 . ', ' . $addrLine2; // address
            $city = explode(',', $addrLine2)[0]; // city
            $state = explode(' ', explode(',', $addrLine2)[1])[1]; // state_cd
            $zip = explode(' ', explode(',', $addrLine2)[1])[2]; // zip5_cd
            $building_name = null; // building_name
        } elseif ($checkAddress) {
            $building_name_temp = Queue::clearText($address[0]->find('h1')[0]->text());
            
            $addrLine2 = Queue::clearText($address[0]->xpath('.//address/text()[preceding-sibling::br]')[0]);
            $city = explode(',', $addrLine2)[0];
            $state = explode(' ', explode(',', $addrLine2)[1])[1];
            $zip = explode(' ', explode(',', $addrLine2)[1])[2];

            $address = Queue::clearText($address[0]->find('address')[0]->text());

            $addrLine1 = str_replace($addrLine2, '', $address);

            $data = [
                "sq",
                "$",
                "bed",
                "bath",
                "sqft",
                "available"
            ];

            foreach ($data as $temp) {
                if (strpos(mb_strtolower($building_name_temp), $temp)) {
                    $building_name = null;
                    break;
                }
                $building_name = $building_name_temp; // building_name
            }
        }

        // pet policy
        $pets = $this->content->xpath(
            "//div[contains(@class, 'HdpContentWrapper') and .//h2[text() = 'Pet policy']]//div[contains(@class, 'HdpContentWrapper-content')]"
        )[0]->find('span.styles__Text-me4q2-0');

        $petPolicy = []; // pet_policy
        foreach ($pets as $pet) {
            $petPolicy[] = Queue::clearText($pet->text());
        }

        // contact

        $phone = isset($this->content->find('div.ContactPhone')[0]) ?
            Queue::clearText($this->content->find('div.ContactPhone')[0]->text()) : ''; // contact_phone
        $contactPerson = isset($this->content->find('div.ContactListedBy-name')[0]) ?
            Queue::clearText($this->content->find('div.ContactListedBy-name')[0]->text()) : '';

        if ($contactPerson === 'Message Contact Manager') {
            $contactPerson = '';
        }

        $contactPerson = str_replace('Message ', '', $contactPerson); // contact_person

        // highlights

        $features = $this->content->find('ul.HdpHighlights-list')[0]->find('li.HdpHighlights-item');

        $onPremiseFeatures = []; // on_premise_features
        foreach ($features as $feature) {
            $onPremiseFeatures[] = Queue::clearText($feature->text());
        }

        // amenities
        $amenitiesSection = isset($this->content->find('div.HdpAmenitySection')[0]) ?
            $this->content->find('div.HdpAmenitySection')[0]->find('li.ListItem') : '';

        if ($amenitiesSection) {
            $amenities = []; // property_info
            foreach ($amenitiesSection as $amenity) {
                $amenities[] = Queue::clearText($amenity->text());
            }
        }

        // descritpion

        $desc = isset($this->content->find('div#HdpDescriptionContent')[0]) ?
            Queue::clearText($this->content->find('div#HdpDescriptionContent')[0]->text()) : ''; // building_desc

        // nearby school

        $schoolsBlock = isset($this->content->find('ul.Schools')[0]) ?
            $this->content->find('ul.Schools')[0]->find('li.SchoolItem') : '';

        $schools = []; // nearby_school
        foreach ($schoolsBlock as $school) {
            $schoolTemp['title'] = Queue::clearText($school->find('h3.SchoolItem-name')[0]->text());
            $schoolTemp['type'] = Queue::clearText($school->find('div.SchoolItem-type')[0]->text());
            $schoolTemp['grade'] = Queue::clearText($school->find('div.SchoolItem-grades')[0]->text());
            $schoolTemp['distance'] = Queue::clearText($school->find('div.SchoolItem-distance')[0]->text());
            $schoolTemp['rating'] = Queue::clearText($school->find('div.SchoolRatingIcon-circle')[0]->text());

            array_push($schools, $schoolTemp);
        }
        
        // availability

        $header = isset($this->content->find('div.SingleModelHdpHeader')[0]) ?
            $this->content->find('div.SingleModelHdpHeader') : '';
        $models = [];

        
        if ($header) { // If there is only one floor plan
            $price = isset($header[0]->find('div.pricing-availability-container')[0]) ?
            Queue::clearText($header[0]->find('div.SingleModelHdpHeader-pricing')[0]->text()) : ''; // listing_price
        
            $status = isset($header[0]->find('div.SingleModelHdpHeader-availability')[0]) ?
            Queue::clearText($header[0]->find('div.SingleModelHdpHeader-availability')[0]->text()) : ''; // status

            $badsBathSqft = isset($header[0]->find('div.BedsBathsSqft')[0]) ?
            $header[0]->find('div.BedsBathsSqft')[0]->find('div.BedsBathsSqft-item') : '';
        
            $beds = Queue::clearText($badsBathSqft[0]->text()); // bedroom_cnt
            $bath = Queue::clearText($badsBathSqft[1]->text()); // bathroom_cnt
            $sqft = Queue::clearText($badsBathSqft[2]->text()); // home_size_sq_ft
        } else { // If there are multiple floor plans
            $multiModels = $this->content->find('div.MultiModelsGroup-container')[0]->find('div.MultiModelsGroup-floorplan-item');

            foreach ($multiModels as $model) {
                $beds = isset($model->find('span.ModelFloorplanItem-detail')[0]) ?
                    Queue::clearText($model->find('span.ModelFloorplanItem-detail')[0]->text()) : ''; // bedroom_cnt
                $bath = isset($model->find('span.ModelFloorplanItem-bthsqft')[0]) ?
                    Queue::clearText($model->find('span.ModelFloorplanItem-bthsqft')[0]->text()) : ''; // bathroom_cnt
                $sqft = isset($model->find('span.ModelFloorplanItem-bthsqft')[1]) ?
                    Queue::clearText($model->find('span.ModelFloorplanItem-bthsqft')[1]->text()) : ''; // home_size_sq_ft
                $image = isset($model->find('div.FloorplanImage-container')[0]) ?
                    $model->find('div.FloorplanImage-container')[0]->find('img.FloorplanImage')[0]
                        ->getAttribute('src') : ''; // image_urls
                
                $modelFloorPlans = $model->find('div.ModelFloorplanItem-unit');

                if ($modelFloorPlans) { // If price block is filled
                    foreach ($modelFloorPlans as $plan) {
                        $price = Queue::clearText($plan->find('div.ModelFloorplanItem-unit-price')[0]->text()); // listing_price
                        $status = Queue::clearText($plan->find('div.ModelFloorplanItem-unit-availability')[0]->text()); // status

                        array_push($models, [
                            'bedroom_cnt' => $beds,
                            'bathroom_cnt' => $bath,
                            'home_size_sq_ft' => $sqft,
                            'listing_price' => $price,
                            'status' => $status,
                            'image_urls' => json_encode($image)
                        ]);
                    }
                } else {
                    $price = Queue::clearText($model->find('div.ModelFloorplanItem-empty-unit-price')[0]->text());

                    array_push($models, [
                        'bedroom_cnt' => $beds,
                        'bathroom_cnt' => $bath,
                        'home_size_sq_ft' => $sqft,
                        'listing_price' => $price,
                        'image_urls' => json_encode($image)
                    ]);
                }
            }
        }

        // images

        $image = $this->content->find('div.PhotoCarousel')[0]->find('li img')[0]->getAttribute('src'); // image_urls

        // database
        $db = new MySql;

        // Filling the fields in the properties table
        $data = [
            'link' => $this->task['link'],
            'image_urls' => json_encode($image),
            'addr_line_2' => $addrLine2,
            'addr_line_1' => $addrLine1,
            'address' => $address,
            'city' => $city,
            'state_cd' => $state,
            'zip5_cd' => $zip,
            'pet_policy' => json_encode($petPolicy),
            'contact_phone' => $phone,
            'contact_person' => $contactPerson,
            'on_premise_features' => json_encode($onPremiseFeatures),
            'property_info' => json_encode($amenities),
            'building_name' => $building_name,
            'building_desc' => $desc,
            'nearby_school' => json_encode($schools),
            'type' => $type
        ];

        // Updating or creating the record in rental
        $idProperty = $db->updateOrCreate('properties', $data);

        // Filling the fields in the availability table
        $data = [
            'listing_price' => $price,
            'bedroom_cnt' => $beds,
            'bathroom_cnt' => $bath,
            'home_size_sq_ft' => $sqft,
            'status' => $status,
            'property_id' => $idProperty[0]
        ];

        // Checking for update
        if ($idProperty[1] === 'update') {
            $db->deleteAvailability($idProperty[0]);
        }
        
        // Adding a record into availability
        if ($models) {
            foreach ($models as $model) {
                $model['property_id'] = $idProperty[0];
                $db->insert('availability', $model);
            }
        } else {
            $db->insert('availability', $data);
        }
        

        echo 'SUCCESS: ' . $idProperty[1] . ' ID prop: ' . $idProperty[0] . PHP_EOL;
        
        return true;
    }
}
