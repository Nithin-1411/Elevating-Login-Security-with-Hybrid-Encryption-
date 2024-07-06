import json
import matplotlib.pyplot as plt
import numpy as np

# Load data from JSON file
with open('data2.json', 'r') as file:
    data = json.load(file)

algorithms = data['algorithms']
times = [data['times'][algo] * 1000 for algo in algorithms]  # Convert times to milliseconds
memories = [data['memories'][algo] for algo in algorithms]
securities = [data['securities'][algo] for algo in algorithms]

# Set up the figure and subplots
fig, ax1 = plt.subplots()

# Plot Time and Memory on the same bar chart with shared x-axis
bar_width = 0.25
index = np.arange(len(algorithms))

bar1 = ax1.bar(index, times, bar_width, label='Time (ms)', color='r')
bar2 = ax1.bar(index + bar_width, memories, bar_width, label='Memory (bytes)', color='b')

# Set the y-axis label for the first axis (Time and Memory)
ax1.set_ylabel('Time (ms) / Memory (bytes)')
ax1.set_xlabel('Algorithms')
ax1.set_title('Comparison of Encryption Algorithms')
ax1.set_xticks(index + bar_width / 2)
ax1.set_xticklabels(algorithms)
ax1.legend(loc='upper left')

# Create a second y-axis for Security
ax2 = ax1.twinx()
bar3 = ax2.bar(index + 2 * bar_width, securities, bar_width, label='Security (score)', color='g')

# Set the y-axis label for the second axis (Security)
ax2.set_ylabel('Security (score)')
ax2.legend(loc='upper right')

# Add data labels to the bars
def add_labels(bars, ax):
    for bar in bars:
        height = bar.get_height()
        ax.annotate('{}'.format(height),
                    xy=(bar.get_x() + bar.get_width() / 2, height),
                    xytext=(0, 3),  # 3 points vertical offset
                    textcoords="offset points",
                    ha='center', va='bottom')

add_labels(bar1, ax1)
add_labels(bar2, ax1)
add_labels(bar3, ax2)

# Show the plot
plt.show()
