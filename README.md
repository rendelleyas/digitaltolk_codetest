## Code

### What makes it amazing code?
- Readability
    : personally, for me the code is readable

### What makes it terrible code?
 - Ununiformed variable naming
    - There are multiple format of variable naming. like '$consumer_type', '$completeddate', and '$adminSenderEmail'
    - Consistency allows others to more easily understand your code.
    - Should avoid using one letter variable use word instead ($k to $key).
 - Unecessary and unutilized variables
    - there are declared variable who are not utilized
    - can confused why is it existing, whats its purpose
 - No comments on the function/event fired who are not readble at first glance 
 - Long one line code, which are not that good to read. Takes time to digest.
 - Multiple logic inside the function which can be separated. 

### How would you have done it
 - #1 Ununiformed variable naming
    - Personaly I prefer camelCase its looks shorter than snake_case with additional '_'.
    - Team should have a code guidelines  - list all the  recommended styles and practices (variable and function namings)
 - #2 Uncomplex condition
    - on simple condition its better to use ternary operator than else if
    ```
        From: 
            if (isset($data['distance']) && $data['distance'] != "") {
                $distance = $data['distance'];
            } else {
                $distance = "";
            }
        =========
        To: 
            $distance = isset($data['distance']) && $data['distance'] != ""? $data['distance'] : "";
    ```
 - #3 Unecessary and unutilized variables
    - remove unecessary or unutilized variables
    ```
        From:
            $response = $this->repository->getUsersJobsHistory($user_id, $request);
            return response($response);
        =========
        To:
            return response($this->repository->getUsersJobsHistory($user_id, $request));
    ```
 - #4 Long one line code. 
    - long line of code is  to hard to read. Add a new line per data or function for readability
    ```
        From:
            $jobs = $cuser->jobs()->with('user.userMeta', 'user.average', 'translatorJobRel.user.average', 'language', 'feedback', 'distance')->whereIn('status', ['completed', 'withdrawbefore24', 'withdrawafter24', 'timedout'])->orderBy('due', 'desc')->paginate(15);
        =========
        To:
            $jobs = $cuser->jobs()
                ->with('user.userMeta', 
                    'user.average', 
                    'translatorJobRel.user.average', 
                    'language', 
                    'feedback', 
                    'distance'
                )->whereIn('status', 
                ['completed', 'withdrawbefore24', 'withdrawafter24', 'timedout'])
                ->orderBy('due', 'desc')
                ->paginate(15);
    ```
 - #5 Function with long lines of code. Multiple logic inside the function 
    - Too many logic inside a function. To make it more readable segregate each function into small function or put it on different class.

### Thoughts on formatting, structure, logic and etc.
 - Personally, the code is readable, I can clearly read it. 
   As I have stated on why is it terrible, those things can also be improved. 
   But, those suggesstion should also be align with the teams guidlines and criteria. Teams guidlines and criteria is the foundation of the good code.
   If everyone is aware then everything is clear.  

