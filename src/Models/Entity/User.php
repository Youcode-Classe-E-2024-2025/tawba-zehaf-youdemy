<?php
namespace Youdemy\Models\Entity;
class User
{
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private string $role;
    private bool $isActive;
    private bool $validated;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    private array $statusChangeLogs = [];
    
    /**
     * User constructor.
     * 
     * @param string $username Username (3-50 characters)
     * @param string $email Valid email address
     * @param string $password Password (min 8 characters)
     * @param string $role User role (default: student)
     * @throws \InvalidArgumentException If validation fails
     */
    public function __construct(string $username, string $email, string $password, string $role = 'student')
    {
        $this->validateUsername($username);
        $this->validateEmail($email);
        $this->validatePassword($password);
        
        $this->username = $username;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->role = $role;
        $this->isActive = true;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getName(): string {

        return $this->username;

    }
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @throws \InvalidArgumentException If username is invalid
     */
    // public function setUsername(string $username): void
    // {
    //     $this->validateUsername($username);
    //     $this->username = $username;
    //     $this->updateTimestamp();
    // }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @throws \InvalidArgumentException If email is invalid
     */
    public function setEmail(string $email): void
    {
        $this->validateEmail($email);
        $this->email = $email;
        $this->updateTimestamp();
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @throws \InvalidArgumentException If password is invalid
     */
    public function setPassword(string $password): void
    {
        $this->validatePassword($password);
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->updateTimestamp();
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
        $this->updateTimestamp();
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
        $this->updateTimestamp();
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Verify if the provided password matches the stored hash
     * 
     * @param string $password
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * Update the timestamp when the entity is modified
     */
    private function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Validate username format
     * 
     * @param string $username
     * @throws \InvalidArgumentException
     */
    private function validateUsername(string $username): void
    {
        if (strlen($username) < 3 || strlen($username) > 50) {
            throw new \InvalidArgumentException('Username must be between 3 and 50 characters');
        }
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            throw new \InvalidArgumentException('Username can only contain letters, numbers, underscores and dashes');
        }
    }

    /**
     * Validate email format
     * 
     * @param string $email
     * @throws \InvalidArgumentException
     */
    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
    }

    /**
     * Validate password strength
     * 
     * @param string $password
     * @throws \InvalidArgumentException
     */
    private function validatePassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters long');
        }
    }



    public function setId(int $id): void {

        $this->id = $id;

    }
    
    
    
    
        public function setCreatedAt(\DateTime $createdAt): void {
    
            $this->createdAt = $createdAt;
    
        }
    
    
        public function setUpdatedAt(\DateTime $updatedAt): void {

            $this->updatedAt = $updatedAt;
    
        }
        public function setUsername(string $username): void {

            $this->username = $username;
            $this->updateTimestamp();
        }    
        public function setName($username): void {

            $this->username = $username;}
            
    // public function isValidated(): bool {

    //     return $this->validated;
    // }
  
    
  
    
        private bool $isValidated;
    
    
    
        public function setIsValidated(bool $isValidated): void {
    
            $this->isValidated = $isValidated;
    
        }
    
    
    
        public function isValidated(): bool {
    
            return $this->isValidated;
    
        }
        public function addStatusChangeLog(string $log): void {
    
            $this->statusChangeLogs[] = $log;
    
        }

            private array $validationLogs = [];
        
        
        
            public function addValidationLog(string $log): void {
        
                $this->validationLogs[] = $log;
        
            }
    
            public function getValidationLogs(): array {
        
                return $this->validationLogs;
        
            }
            
            
            
                private ?\DateTime $validatedAt = null;
            
            
            
                public function setValidatedAt(\DateTime $validatedAt): void {
            
                    $this->validatedAt = $validatedAt;
            
                }
            
            
            
                public function getValidatedAt(): ?\DateTime {
            
                    return $this->validatedAt;
            
                }
            
            }
            
    
    
    
  


    