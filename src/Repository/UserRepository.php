<?php

namespace Youdemy\Repository;

use Youdemy\Config\Database;
use Youdemy\Models\Entity\User;
use PDOException;
use PDO;
use DateTime;

class UserRepository {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function save(User $user): void {
        try {
            if ($user->getId()) {
                $this->update($user);
            } else {
                $this->create($user);
            }
        } catch (PDOException $e) {
            throw new \RuntimeException('Failed to save user: ' . $e->getMessage());
        }
    }

    // public function findById(int $id): ?User {
    //     $query = "SELECT * FROM users WHERE id = :id";
    //     $result = $this->db->query($query, ['id' => $id])->fetch();

    //     if (!$result) {
    //         return null;
    //     }

    //     return $this->hydrateUser($result);
    // }
    public function findById($id): ?User {
        $query = "SELECT username, email, password, role FROM users WHERE id = :id"; // Include password
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return $this->hydrateUser($data);
        }
        return null; // Return null if no user found
    }

    public function findByEmail(string $email): ?User {
        $query = "SELECT * FROM users WHERE email = :email";
        $result = $this->db->query($query, ['email' => $email])->fetch();

        if (!$result) {
            return null;
        }

        return $this->hydrateUser($result);
    }

    public function findTeachers(): array {
        $query = "SELECT * FROM users WHERE role = 'teacher' ORDER BY name";
        $results = $this->db->query($query)->fetchAll();
        return array_map([$this, 'hydrateUser'], $results);
    }

    public function findStudents(): array {
        $query = "SELECT * FROM users WHERE role = 'student' ORDER BY name";
        $results = $this->db->query($query)->fetchAll();
        return array_map([$this, 'hydrateUser'], $results);
    }

    private function create(User $user): void {
        $now = new DateTime();
        $params = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRole()
        ];

        $query = "INSERT INTO users (name, email, password, role) 
                 VALUES (:name, :email, :password, :role)";
        
        $this->db->query($query, $params);
        $user->setId($this->db->lastInsertId());
        $user->setCreatedAt($now);
        $user->setUpdatedAt($now);
    }

    private function update(User $user): void {
        $now = new DateTime();
        $params = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ];

        // Only include password in update if it's been changed
        if ($user->getPassword()) {
            $params['password'] = $user->getPassword();
            $passwordSet = ", password = :password";
        } else {
            $passwordSet = "";
        }

        $query = "UPDATE users 
                 SET name = :name,
                     email = :email,
                     role = :role,
                     " . 
                     $passwordSet . 
                 " WHERE id = :id";

        $result = $this->db->query($query, $params)->rowCount();
        
        if ($result === 0) {
            throw new \RuntimeException('User not found or no changes made');
        }
        
        $user->setUpdatedAt($now);
    }
    public function hydrateUser(array $data): User {
        $username = isset($data['username']) ? $data['username'] : '';
        $email = isset($data['email']) ? $data['email'] : '';
        $password = isset($data['password']) ? $data['password'] : ''; // Ensure this is fetched correctly
        $role = isset($data['role']) ? $data['role'] : '';
    
        // Check if the password is valid
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException("Password must be at least 8 characters long");
        }
    
        return new User($username, $email, $password, $role);
    }
            public function getTotalCount(): int {
        
                $query = "SELECT COUNT(*) as total FROM users";
        
                $result = $this->db->query($query)->fetch();
        
                return (int) $result['total'];
        
            }
        
            
            
            
                public function countActiveAdmins(): int {
            
                    $query = "SELECT COUNT(*) FROM users WHERE role = 'admin' AND is_active = 1";
            
                    return (int) $this->db->query($query)->fetchColumn();
            
                }
    
                
                
                    public function getAllUsers(int $page, int $perPage, ?string $role = null): array {
                
                        $offset = ($page - 1) * $perPage;
                
                        $query = "SELECT * FROM users";
                
                        $params = [];
                
                
                
                        if ($role !== null) {
                
                            $query .= " WHERE role = :role";
                
                            $params['role'] = $role;
                
                        }
                
                
                
                        $query .= " LIMIT :limit OFFSET :offset";
                
                        $params['limit'] = $perPage;
                
                        $params['offset'] = $offset;
                
                
                
                        return $this->db->query($query, $params)->fetchAll();
                
                    }
                
                    
                    
                        public function getNewUsersByDateRange(DateTime $startDate, DateTime $endDate): array {
                    
                            $query = "SELECT * FROM users WHERE created_at BETWEEN :start_date AND :end_date";
                    
                            $params = [
                    
                                'start_date' => $startDate->format('Y-m-d H:i:s'),
                    
                                'end_date' => $endDate->format('Y-m-d H:i:s')
                    
                            ];
                    
                            return $this->db->query($query, $params)->fetchAll();
                    
                        }
                   


    public function deleteInactiveUsers(DateTime $cutoffDate): void {

        $query = "DELETE FROM users WHERE last_active < :cutoff_date AND is_active = 0";

        $this->db->query($query, ['cutoff_date' => $cutoffDate->format('Y-m-d H:i:s')]);

    }
    
        public function count(): int {
    
            $query = "SELECT COUNT(*) as total FROM users";
    
            $result = $this->db->query($query)->fetch();
    
            return (int) $result['total'];
    
        }

    public function countByRole(string $role): int {

        $query = "SELECT COUNT(*) as count FROM users WHERE role = :role";

        $result = $this->db->query($query, ['role' => $role])->fetch();

        return (int) $result['count'];

    }
   
    
    
        public function countNewUsersSince(DateTime $since): int {
    
            $query = "SELECT COUNT(*) as total FROM users WHERE created_at >= :since";
    
            $params = ['since' => $since->format('Y-m-d H:i:s')];
    
            return (int) $this->db->query($query, $params)->fetch()['total'];
    
        }
        

    public function countActiveTeachersSince(DateTime $startDate, DateTime $endDate): int {

        $query = "SELECT COUNT(*) FROM users WHERE role = 'teacher' AND last_active BETWEEN :start_date AND :end_date";

        $params = [

            'start_date' => $startDate->format('Y-m-d H:i:s'),

            'end_date' => $endDate->format('Y-m-d H:i:s')

        ];

        return (int) $this->db->query($query, $params)->fetchColumn();

    }
   
    
    
        public function countRetainedTeachersSince(DateTime $since): int {
    
            $query = "SELECT COUNT(*) FROM users WHERE role = 'teacher' AND last_active >= :since";
    
            $params = ['since' => $since->format('Y-m-d H:i:s')];
    
            return (int) $this->db->query($query, $params)->fetchColumn();
    
        }
    

    public function countActiveStudentsSince(DateTime $since): int {

        $query = "SELECT COUNT(*) FROM users WHERE role = 'student' AND last_active >= :since";

        $params = ['since' => $since->format('Y-m-d H:i:s')];

        return (int) $this->db->query($query, $params)->fetchColumn();

    }

}

    
    


    
    