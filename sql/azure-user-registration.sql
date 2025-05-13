-- Create table for user registration
CREATE TABLE tbl_member (
    id INT IDENTITY(1,1) PRIMARY KEY,
    username NVARCHAR(255) NOT NULL,
    password NVARCHAR(200) NOT NULL,
    email NVARCHAR(255) NOT NULL,
    create_at DATETIME2 DEFAULT GETDATE()
);

-- Create indexes for better performance
CREATE INDEX idx_username ON tbl_member(username);
CREATE INDEX idx_email ON tbl_member(email); 