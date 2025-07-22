<?php
/**
 * LearnerModel.php
 * 
 * Model for learner data
 */

namespace WeCoza\Models\Learner;

class LearnerModel {
    /**
     * Learner properties
     */
    private $id;
    private $firstName;
    private $lastName;
    private $email;
    private $phone;
    private $idNumber;
    private $dateOfBirth;
    private $address;
    private $city;
    private $postalCode;
    private $province;
    private $country;
    private $createdAt;
    private $updatedAt;

    /**
     * Constructor
     */
    public function __construct($data = null) {
        if ($data) {
            $this->hydrate($data);
        }
    }

    /**
     * Hydrate model with data
     * 
     * @param array $data Data to populate model
     */
    public function hydrate($data) {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * Get learner by ID
     * 
     * @param int $id Learner ID
     * @return LearnerModel|null
     */
    public static function getById($id) {
        // Implementation will be added later
        return null;
    }

    /**
     * Save learner data
     * 
     * @return bool Success status
     */
    public function save() {
        // Implementation will be added later
        return false;
    }

    /**
     * Update learner data
     * 
     * @return bool Success status
     */
    public function update() {
        // Implementation will be added later
        return false;
    }

    /**
     * Delete learner
     * 
     * @return bool Success status
     */
    public function delete() {
        // Implementation will be added later
        return false;
    }

    // Getters and setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }

    public function getIdNumber() {
        return $this->idNumber;
    }

    public function setIdNumber($idNumber) {
        $this->idNumber = $idNumber;
        return $this;
    }

    public function getDateOfBirth() {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth($dateOfBirth) {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
        return $this;
    }

    public function getPostalCode() {
        return $this->postalCode;
    }

    public function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getProvince() {
        return $this->province;
    }

    public function setProvince($province) {
        $this->province = $province;
        return $this;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setCountry($country) {
        $this->country = $country;
        return $this;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
