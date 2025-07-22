-- WeCoza Learners Management Database Schema
-- This file documents the database structure for the learners management system
-- 
-- Note: These tables appear to already exist in the database.
-- This schema is documented here for reference when converting to a plugin.

-- ============================================================================
-- Table: learners
-- Main table storing learner information
-- ============================================================================
CREATE TABLE IF NOT EXISTS learners (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    initials VARCHAR(10),
    surname VARCHAR(100) NOT NULL,
    gender VARCHAR(20),
    race VARCHAR(50),
    sa_id_no VARCHAR(20),
    passport_number VARCHAR(50),
    tel_number VARCHAR(20),
    alternative_tel_number VARCHAR(20),
    email_address VARCHAR(255),
    address_line_1 VARCHAR(255),
    address_line_2 VARCHAR(255),
    city_town_id INTEGER,
    province_region_id INTEGER,
    postal_code VARCHAR(10),
    highest_qualification INTEGER,
    assessment_status VARCHAR(50),
    placement_assessment_date DATE,
    numeracy_level INTEGER,
    communication_level INTEGER,
    employment_status BOOLEAN DEFAULT FALSE,
    employer_id INTEGER,
    disability_status BOOLEAN DEFAULT FALSE,
    scanned_portfolio TEXT, -- Stores comma-separated list of portfolio file paths
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraints (if applicable)
    FOREIGN KEY (city_town_id) REFERENCES locations(location_id),
    FOREIGN KEY (province_region_id) REFERENCES locations(location_id),
    FOREIGN KEY (highest_qualification) REFERENCES learner_qualifications(id),
    FOREIGN KEY (employer_id) REFERENCES employers(employer_id),
    FOREIGN KEY (numeracy_level) REFERENCES learner_placement_level(placement_level_id),
    FOREIGN KEY (communication_level) REFERENCES learner_placement_level(placement_level_id)
);

-- ============================================================================
-- Table: learner_portfolios
-- Stores individual portfolio file uploads for learners
-- ============================================================================
CREATE TABLE IF NOT EXISTS learner_portfolios (
    portfolio_id SERIAL PRIMARY KEY,
    learner_id INTEGER NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (learner_id) REFERENCES learners(id) ON DELETE CASCADE
);

-- ============================================================================
-- Table: learner_qualifications
-- Reference table for educational qualifications
-- ============================================================================
CREATE TABLE IF NOT EXISTS learner_qualifications (
    id SERIAL PRIMARY KEY,
    qualification VARCHAR(255) NOT NULL UNIQUE
);

-- ============================================================================
-- Table: learner_placement_level
-- Reference table for assessment placement levels
-- ============================================================================
CREATE TABLE IF NOT EXISTS learner_placement_level (
    placement_level_id SERIAL PRIMARY KEY,
    level VARCHAR(50) NOT NULL UNIQUE
);

-- ============================================================================
-- Table: locations
-- Reference table for cities, towns, and provinces
-- ============================================================================
CREATE TABLE IF NOT EXISTS locations (
    location_id SERIAL PRIMARY KEY,
    suburb VARCHAR(255),
    town VARCHAR(255),
    province VARCHAR(255),
    postal_code VARCHAR(10)
);

-- ============================================================================
-- Table: employers
-- Reference table for employer information
-- ============================================================================
CREATE TABLE IF NOT EXISTS employers (
    employer_id SERIAL PRIMARY KEY,
    employer_name VARCHAR(255) NOT NULL
);

-- ============================================================================
-- Indexes for performance optimization
-- ============================================================================
CREATE INDEX idx_learners_email ON learners(email_address);
CREATE INDEX idx_learners_id_number ON learners(sa_id_no);
CREATE INDEX idx_learners_surname ON learners(surname);
CREATE INDEX idx_learner_portfolios_learner_id ON learner_portfolios(learner_id);
CREATE INDEX idx_locations_town ON locations(LOWER(town));
CREATE INDEX idx_locations_province ON locations(LOWER(province));

-- ============================================================================
-- Sample data for reference tables (if needed)
-- ============================================================================
-- INSERT INTO learner_qualifications (qualification) VALUES
-- ('No Formal Education'),
-- ('Primary Education'),
-- ('Grade 10'),
-- ('Grade 12'),
-- ('Certificate'),
-- ('Diploma'),
-- ('Degree'),
-- ('Postgraduate');

-- INSERT INTO learner_placement_level (level) VALUES
-- ('N1 - Numeracy Level 1'),
-- ('N2 - Numeracy Level 2'),
-- ('N3 - Numeracy Level 3'),
-- ('N4 - Numeracy Level 4'),
-- ('C1 - Communication Level 1'),
-- ('C2 - Communication Level 2'),
-- ('C3 - Communication Level 3'),
-- ('C4 - Communication Level 4');