<?php
ini_set('display_errors', 'stderr');

# arrays of instructions which are divided to groups by number and types of their  arguments
$array = array('CREATEFRAME', 'PUSHFRAME', 'POPFRAME', 'RETURN', 'BREAK', 'MOVE', 'INT2CHAR', 'STRLEN', 'TYPE', 'DEFVAR', 'POPS', 'CALL', 'LABEL', 'JUMP', 'PUSHS', 'WRITE', 'EXIT', 'DPRINT', 'LT', 'GT', 'EQ', 'OR', 'NOT', 'STRI2INT', 'CONCAT', 'GETCHAR', 'SETCHAR', 'JUMPIFEQ', 'JUMPIFNEQ', 'ADD', 'SUB', 'MUL', 'IDIV', 'READ', 'NOT');
$array_case1 = array('CREATEFRAME', 'PUSHFRAME', 'POPFRAME', 'RETURN', 'BREAK');
$array_case2 = array('MOVE', 'INT2CHAR', 'STRLEN', 'TYPE');
$array_case3 = array('DEFVAR', 'POPS');
$array_case4 = array('CALL', 'LABEL', 'JUMP');
$array_case5 = array('PUSHS', 'WRITE', 'EXIT', 'DPRINT');
$array_case6 = array('ADD', 'SUB', 'MUL', 'IDIV', 'LT', 'GT', 'EQ', 'OR', 'AND', 'STRI2INT', 'CONCAT', 'GETCHAR', 'SETCHAR');
$array_case7 = array('READ');
$array_case8 = array('JUMPIFEQ', 'JUMPIFNEQ');
$array_case9 = array('NOT');

/**
 * Class Instruction which holds current instruction opcode and its arguments
 */
class Instruction {

    private $array_of_types = array('int', 'string', 'bool', 'nil');

    public function __construct($splitted, $order) {
        $this->order = $order;
        $this->opcode = $splitted[0];
        $this->arg1 = isset($splitted[1]) ? $splitted[1] : null;
        $this->arg2 = isset($splitted[2]) ? $splitted[2] : null;
        $this->arg3 = isset($splitted[3]) ? $splitted[3] : null;
    }

    /**
     * Method printing the beginning of an instruction
     */
    public function print_instruction() {
        echo("\t<instruction order=\"$this->order\" opcode=\"".strtoupper($this->opcode)."\">\n");
    }

    /**
     * Method printing first argument
     * 
     * @param string $type symbol type / var
     */
    public function print_arg1($type) {
        if(in_array($type, $this->array_of_types)) {
            $splittedd = explode('@', trim($this->arg1, "\n"), 2);
            echo("\t\t<arg1 type=\"$type\">$splittedd[1]</arg1>\n");
        } else if ($type == "label" or $type == "type" ) {
            echo("\t\t<arg1 type=\"$type\">$this->arg1</arg1>\n");
        } else {
            echo("\t\t<arg1 type=\"var\">$this->arg1</arg1>\n");
        }
    }

     /**
     * Method printing second argument
     * 
     * @param string $type symbol type / var
     */
    public function print_arg2($type) {
         if(in_array($type, $this->array_of_types))  {
            $splittedd = explode('@', trim($this->arg2, "\n"), 2);
            echo("\t\t<arg2 type=\"$type\">$splittedd[1]</arg2>\n");
        } else if ($type == "label" or $type == "type" ) {
            echo("\t\t<arg2 type=\"$type\">$this->arg2</arg2>\n");
        } else {
            echo("\t\t<arg2 type=\"var\">$this->arg2</arg2>\n");
        }    
    }

     /**
     * Method printing the third argument
     * 
     * @param string $type symbol type / var
     */
    public function print_arg3($type) {
        if(in_array($type, $this->array_of_types)) {
            $splittedd = explode('@', trim($this->arg3, "\n"), 2);
            echo("\t\t<arg3 type=\"$type\">$splittedd[1]</arg3>\n");
        } else if ($type == "label" or $type == "type" ) {
            echo("\t\t<arg3 type=\"$type\">$this->arg3</arg3>\n");
        } else {
            echo("\t\t<arg3 type=\"var\">$this->arg3</arg3>\n");
        }
    }

    /**
    * Method returning type of a symbol (int, string, bool, nil etc.)
    * 
    * @param string $arg constant, variable
    * @return string 
    */
    public function get_type($arg) {
        $arg = explode('@', trim($arg, "\n"));
        return $arg[0];
    }

    /**
     * Method printing end of instruction
     */
    public function print_instruction_end() {
        echo("\t</instruction>\n");
    }
}

# argument checking
if ($argc > 1) {
    

    if ($argc == 2 && $argv[1] == '--help') {
        echo(
        "------------------------------------------------------------------------------------------------------------------------"    
        ."This program reads an IPPcode22 program from the standard input (stdin), checks its lexical and syntactical"
        . " correctness and outputs an XML representation of the input program to the standard output (stdout).\n\n"
        . "Possible error return codes:\n  - 10: invalid command line arguments\n"
        . "  - 21: missing header of the input file\n  - 22: invalid instruction code in the input file\n"
        . "  - 23: other lexical or syntactic error (e.g. invalid instruction argument)\n"
        . "------------------------------------------------------------------------------------------------------------------------");
        exit(0);
    } else {
        exit(10);
    } 
}

/**
 * Function to cut comments at the end of the line
 *
 * @param string $line line to be cut
 * @return string
 */ 
function cut_string($line) {   
    $length = strlen($line);
    $line_new = null;
    for ($i = 0; $i < $length; $i++) { #loop to check an occurance of a "#" symbol which indicates the beginning of a comment

        if ($line[$i] != '#') {
            $line_new = $line_new.$line[$i];
        } else { 
            break;
        }
    }
    $parse = false;
    return $line_new;
}

/**
 * Function to check the correct number of arguments for each instruction
 *
 * @param string $line current instruction and arguments which need to be checked
 */ 
function check_inst_args($line){

    $num = 0; # number of argumets
    foreach($line as $counter) { # counts number of arguments and than compare in "switch-case" with the correct number of arguments
        $num++;
    }

    global $array_case1, $array_case2, $array_case3, $array_case4, $array_case5, $array_case6, $array_case7, $array_case8, $array_case9;

    switch(strtoupper($line[0])) {
        case(in_array($line[0], $array_case1)):
            if($num != 1) exit(23);
            break;
        case(in_array($line[0], $array_case2)):
            if($num != 3) exit(23);
            break;
        case(in_array($line[0], $array_case3)):
            if($num != 2) exit(23);
            break;
        case(in_array($line[0], $array_case4)):
            if($num != 2) exit(23);
            break;
        case(in_array($line[0], $array_case5));
            if($num != 2) exit(23);
            break;
        case(in_array($line[0], $array_case6)):
            if($num != 4) exit(23);
            break;
        case(in_array($line[0], $array_case7)):
            if($num != 3) exit(23);
            break;
        case(in_array($line[0], $array_case8)):
            if($num != 4) exit(23);
            break;
        case(in_array($line[0], $array_case9)):
            if($num != 3) exit(23);
            break;
        default:
            break;
        }   
}

/**
 * Returns regular expression to be checked
 *
 * @param string $type type of argument determines which regular expression should be returned
 * @return string regular expression
 */ 
function regex_match($type){

    switch(strtoupper($type)) {
        case("VAR"):
            return '/(GF|TF|LF)@([a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*)[ \t]*/';
        case("SYMB"):
            return '/(?:(GF|TF|LF)@([a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*)|^(int)@([+-]?[0-9]+)$|^(int)@(0x[0-9a-fA-F]+)$|^(int)@(0o[0-7]+)$|^(bool)@(true|false)$|^(nil)@(nil)$|^(string)@(\\\\\d{3}|[^\\\\\s])*$)/';
        case("LABEL"):
            return '/^([a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*)$/';
        case("TYPE"):
            return '/^(int|string|bool)$[ \t]*/';
        default:
            exit(23);
        }
}

$parse = false; # gives premission to start parsing the program
$started = false; # indicates occurance of header
$insnum = 1; # instruction number == order
# main loop processing instructions 

while ($line = fgets(STDIN)) {

    $line = cut_string($line); # cuts strings from the end of the line
    while($line == "\n" or $line == NULL or ctype_space($line)) { # analysis of empty lines
        $line = fgets(STDIN);
        $line = cut_string($line); # cuts strings from the end of the line
        if(feof(STDIN) && $line == NULL) {
            
            if($started) {
                echo("</program>\n");
            }
            
            exit(0);
        }       
    
   }

   
    if (!$started) {
        $line = trim($line, " ");
        if($line == ".IPPcode23" or $line == ".IPPcode23\n") {
            $parse = true; # header checked, parser is ready to parse instructions
            echo('<?xml version="1.0" encoding="UTF-8"?>'."\n");
            echo("<program language=\"IPPcode23\">\n");
            
        } else {
            fwrite(STDERR, "ERROR > Wrong or no header!\n");
            exit(21);
        }
    }

    if($parse == true) {
        $line = trim($line, "\n"); 
        $splitted = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY); # splitting arguments into an array
        $splitted[0] = strtoupper($splitted[0]); # translating instruction to uppercase
        $splitted = array_filter($splitted);  # checking empty arguments
        check_inst_args($splitted); # checking number of arguments of the current instruction
  
        $j = 0; 
        foreach($splitted as $i) {  #replacing symbols which are problematic for an XML file       
            $splitted[$j] = str_replace('&', "&amp;", $splitted[$j]);
            $splitted[$j] = str_replace('<', "&lt;", $splitted[$j]);
            $splitted[$j] = str_replace('>', "&gt;", $splitted[$j]);
            $splitted[$j] = str_replace('"', "&quot;", $splitted[$j]);
            $splitted[$j] = str_replace('\'', "&apos;", $splitted[$j]);
            $j++;
        }
             
        $ins = new Instruction($splitted, $insnum); # creating new instance of a class Instruction
        switch ($splitted[0]) { 
            
            # if the instruction is located in the first group of instrucitons, execute this part of code
            case(in_array($splitted[0], $array_case1)):  
                $ins->print_instruction(); 
                $ins->print_instruction_end();
                $insnum++; 
                break;

            case(in_array($splitted[0], $array_case2)):
                $ins->print_instruction();
                $type1 = $ins->get_type($splitted[2]); 
                preg_match(regex_match("VAR"), $splitted[1]) ? $ins->print_arg1("var") : exit(23);
                preg_match(regex_match("SYMB"), $splitted[2]) ? $ins->print_arg2($type1) : exit(23);
                $ins->print_instruction_end();
                $insnum++;
                break;

            case(in_array($splitted[0], $array_case3)):
                $ins->print_instruction();
                preg_match(regex_match("VAR"), $splitted[1]) ? $ins->print_arg1("var") : exit(23);
                $ins->print_instruction_end();
                $insnum++;
                break;

            case(in_array($splitted[0], $array_case4)):
                $ins->print_instruction();
                preg_match(regex_match("LABEL"), $splitted[1]) ? $ins->print_arg1("label") : exit(23);
                $ins->print_instruction_end();
                $insnum++;
                break;

            case(in_array($splitted[0], $array_case5)):
                $ins->print_instruction();
                $type1 = $ins->get_type($splitted[1]);
                preg_match(regex_match("SYMB"), $splitted[1]) ? $ins->print_arg1($type1) : exit(23);
                $ins->print_instruction_end();
                $insnum++;
                break;
                
            case(in_array($splitted[0], $array_case6)):
                $ins->print_instruction();
                $type2 = $ins->get_type($splitted[2]);
                $type3 = $ins->get_type($splitted[3]);
                preg_match(regex_match("VAR"),  $splitted[1]) ? $ins->print_arg1("var")  : exit(23);
                preg_match(regex_match("SYMB"), $splitted[2]) ? $ins->print_arg2($type2) : exit(23);
                preg_match(regex_match("SYMB"), $splitted[3]) ? $ins->print_arg3($type3) : exit(23);
                $ins->print_instruction_end();
                $insnum++;
                break;

            case(in_array($splitted[0], $array_case7)):
                $ins->print_instruction();
                $type1 = $ins->get_type("type");
                preg_match(regex_match("VAR"),  $splitted[1]) ? $ins->print_arg1("var")  : exit(23);
                preg_match(regex_match("TYPE"), $splitted[2]) ? $ins->print_arg2($type1) : exit(23);
                $ins->print_instruction_end();
                $insnum++;
                break;

            case(in_array($splitted[0], $array_case8)):
                $ins->print_instruction();
                $type2 = $ins->get_type($splitted[2]);
                $type3 = $ins->get_type($splitted[3]);
                preg_match(regex_match("LABEL"), $splitted[1]) ? $ins->print_arg1("label") : exit(23);
                preg_match(regex_match("SYMB"), $splitted[2]) ? $ins->print_arg2($type2) : exit(23);
                preg_match(regex_match("SYMB"), $splitted[3]) ? $ins->print_arg3($type3) : exit(23);
                $ins->print_instruction_end();
                $insnum++;
                break;

            case(in_array($splitted[0], $array_case9)):
                $ins->print_instruction();
                $type2 = $ins->get_type($splitted[2]);
                preg_match(regex_match("VAR"),  $splitted[1]) ? $ins->print_arg1("var")  : exit(23);
                preg_match(regex_match("SYMB"), $splitted[2]) ? $ins->print_arg2($type2) : exit(23);
                $insnum++;
                $ins->print_instruction_end();
                break;
                
            default:          
                if($splitted[0] == strtoupper(".IPPCODE23") && $started == false) {
                    $started = true; 
                    break;
                } elseif (!in_array($splitted[0], $array)) {
                    exit(22);
                }
                break;
        }
    }
}

if(feof(STDIN)) {
    if($started) {
        echo("</program>\n");
    }
           
    exit(0);
}
?>