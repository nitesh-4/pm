document.getElementById('project-form').addEventListener('submit', submitProject);

        function submitProject(event) {
            event.preventDefault(); // Prevent default form submission

            // Collect form data
            const formData = new FormData(document.getElementById('project-form'));

            // Send an AJAX request
            fetch('php/create_project.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    // Show a pop-up message
                    alert(data.message);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }


        document.getElementById('addTaskButton').addEventListener('click', function (event) {
            addTask(event);
        });
        function addTask(event) {
            event.preventDefault(); // Prevent default button action
            console.log("Hi");
            // Fetch members to populate the "Assign to" dropdown
            fetch('php/get_members.php')
                .then(response => response.json())
                .then(members => {
                    const taskContainer = document.getElementById('tasks-container');

                    // Create form elements
                    const taskDiv = document.createElement('div');
                    taskDiv.className = 'task';

                    const taskNameInput = document.createElement('input');
                    taskNameInput.type = 'text';
                    taskNameInput.name = 'task_name[]';
                    taskNameInput.placeholder = 'Task Name';
                    taskDiv.appendChild(taskNameInput);

                    const taskDescriptionInput = document.createElement('textarea');
                    taskDescriptionInput.name = 'description[]';
                    taskDescriptionInput.placeholder = 'Description';
                    taskDiv.appendChild(taskDescriptionInput);

                    const assignToSelect = document.createElement('select');
                    assignToSelect.name = 'assign_to[]';
                    members.forEach(member => {
                        const option = document.createElement('option');
                        option.value = member.UserId;
                        option.text = member.name;
                        assignToSelect.appendChild(option);
                    });
                    taskDiv.appendChild(assignToSelect);

                    const statusInput = document.createElement('input');
                    statusInput.type = 'text';
                    statusInput.name = 'status[]';
                    statusInput.placeholder = 'Status';
                    taskDiv.appendChild(statusInput);

                    // Append the new task form to the task container
                    taskContainer.appendChild(taskDiv);
                });
        }