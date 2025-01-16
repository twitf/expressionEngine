1. 变量命名规则
   字母开头：变量名必须以字母（a-z 或 A-Z）开头。
   允许字母、数字和下划线：变量名可以包含字母、数字和下划线（_）。
   区分大小写：变量名区分大小写（例如，varName 和 VarName 是两个不同的变量）。
2. 变量声明
   显式声明：使用特定的关键字来声明变量，例如  或 var。
   类型推断：允许语言自动推断变量类型，减少冗余。
3. 变量赋值
   简单赋值：使用等号（=）进行赋值。
   示例设计
   以下是一个示例脚本语言的变量格式设计：

# 变量声明和赋值
variableName = 10
anotherVariable = "Hello, World!"
isReady = true

# 支持类型推断
inferredVariable = 3.14

# 支持下划线和数字
user_age = 25
userName1 = "Alice"

# 区分大小写
varName = "lowercase"
VarName = "uppercase"
4. 变量类型
   基本类型：支持常见的基本类型，如整数（int）、浮点数（float）、字符串（string）、布尔值（bool）。
   集合类型：支持数组（array）、字典（dictionary）等集合类型。
5. 变量作用域
   局部变量：在函数或代码块内声明的变量，其作用域仅限于该函数或代码块。
   全局变量：在脚本的顶层声明的变量，可以在整个脚本中访问。
   示例代码
# 全局变量
globalVar = "I am global"

function exampleFunction() {
# 局部变量
localVar = "I am local"
print(localVar)
print(globalVar)
}

exampleFunction()
print(globalVar)
# print(localVar) # 这行代码会报错，因为 localVar 是局部变量
6. 变量初始化
   默认值：未初始化的变量可以有默认值（例如，整数默认值为 0，字符串默认值为空字符串）。
7. 常量
   常量声明：使用 const 关键字声明常量，常量一旦赋值后不可更改。
   const PI = 3.14159
   const greeting = "Hello, World!"


# 词法分析
DFA算法进行词法分析生成标记

以下是词法分析器的Token类型。以及每种Token类型的解释：
Operator: 单个操作符，如+, -, *, /, =, <, >, !, &, |。
DoubleOperator: 双字符操作符，如==, !=, <=, >=, &&, ||。
Symbol: 符号，如(, ), {, }, [, ], ;, ,, .。
Whitespace: 空白字符（不包括换行符）。
LineFeed: 换行符（\n）。
Keyword: 关键字，如if, else, elseif, in, not。
Identifier: 标识符（变量名等）。
Number: 整数。
Float: 浮点数。
String: 字符串。
Char: 单个字符。
Unknown: 未知字符。

# 语法分析
普拉特分析法进行语法分析生成抽象语法树（AST）

# 解析(求值或处理AST)



