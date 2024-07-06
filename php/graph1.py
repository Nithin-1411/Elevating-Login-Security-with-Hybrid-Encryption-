import matplotlib.pyplot as plt
import json

# Load your data from data.json
with open('data2.json', 'r') as f:
    data = json.load(f)

algorithms = data['algorithms']
times = [data['times'][algo] for algo in algorithms]
memories = [data['memories'][algo] for algo in algorithms]
securities = [data['securities'][algo] for algo in algorithms]

# Create the bar graph for execution time
plt.figure(figsize=(10, 6))
plt.bar(algorithms, times, color='skyblue')
plt.xlabel('Algorithm')
plt.ylabel('Execution Time (seconds)')
plt.title('Algorithm Execution Time Comparison')
plt.show()

# Create the bar graph for memory usage
plt.figure(figsize=(10, 6))
plt.bar(algorithms, memories, color='skyblue')
plt.xlabel('Algorithm')
plt.ylabel('Memory Usage (bytes)')
plt.title('Algorithm Memory Usage Comparison')
plt.yscale('log')  # Use log scale for better visualization if the values vary a lot
plt.show()

plt.figure(figsize=(10, 6))
plt.bar(algorithms, securities, color='skyblue')
plt.xlabel('Algorithm')
plt.ylabel('Security Unit')
plt.title('Algorithm Security Comparison')
# plt.yscale('log')  # Use log scale for better visualization if the values vary a lot
plt.show()